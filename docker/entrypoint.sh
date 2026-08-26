#!/usr/bin/env bash
# =============================================================================
# SIAKAD NUJA — Entrypoint produksi (Railway-ready)
#
# Mengorkestrasi semua proses di dalam satu container:
#   1. Menunggu database siap
#   2. Migrasi + cache konfigurasi + symlink storage
#   3. WhatsApp sidecar (Node/Chromium) + watchdog
#   4. SSE listener  : whatsapp:web:listen <session>
#   5. Queue worker  : queue:work
#   6. Web server    : php artisan serve --port=$PORT  (foreground)
#
# Variabel kendali (semua opsional):
#   RUN_MIGRATIONS=true|false     jalankan migrate --force (default true)
#   START_QUEUE=true|false        jalankan queue worker     (default true)
#   START_SIDECAR=true|false      jalankan WA sidecar       (ikut WHATSAPP_WEB_ENABLED)
#   WHATSAPP_SESSION_ID=main      id sesi untuk SSE listener (default main)
#   DB_WAIT_TIMEOUT=90            detik maks menunggu DB    (default 90)
# =============================================================================
set -euo pipefail

cd /var/www/html

log()      { echo "[entrypoint] $(date '+%Y-%m-%d %H:%M:%S') $*"; }
warn()     { log "WARNING: $*"; }

export PORT="${PORT:-8080}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
START_QUEUE="${START_QUEUE:-true}"
START_SIDECAR="${START_SIDECAR:-auto}"
WHATSAPP_SESSION_ID="${WHATSAPP_SESSION_ID:-main}"
DB_WAIT_TIMEOUT="${DB_WAIT_TIMEOUT:-90}"

SIDECAR_PID_FILE="${WHATSAPP_WEB_PID_FILE:-storage/app/whatsapp-sidecar/sidecar.pid}"

# -----------------------------------------------------------------------------
# 1. Tunggu database siap
# -----------------------------------------------------------------------------
if [[ "${DB_CONNECTION:-mysql}" == mysql || "${DB_CONNECTION:-}" == mariadb ]]; then
    host="${DB_HOST:-127.0.0.1}"
    port="${DB_PORT:-3306}"
    log "Menunggu database ${host}:${port} (maks ${DB_WAIT_TIMEOUT}s)..."
    ready=0
    for ((i = 1; i <= DB_WAIT_TIMEOUT; i++)); do
        if timeout 2 bash -c "exec </dev/tcp/${host}/${port}" 2>/dev/null; then
            ready=1
            break
        fi
        sleep 1
    done
    [[ $ready -eq 1 ]] || { log "Database tidak merespons dalam ${DB_WAIT_TIMEOUT}s."; exit 1; }
    log "Database siap."
fi

# -----------------------------------------------------------------------------
# 2. APP_KEY — generate darurat bila belum diset (WAJIB di-set sebagai variabel!)
# -----------------------------------------------------------------------------
if [[ -z "${APP_KEY:-}" ]]; then
    export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    warn "APP_KEY kosong -> dibuat sementara untuk boot ini."
    warn "Enkripsi (cookie/session) akan rusak setiap redeploy."
    warn "Set variabel APP_KEY permanen di Railway segera!"
fi

# -----------------------------------------------------------------------------
# 3. Struktur storage (penting saat volume Railway masih kosong)
# -----------------------------------------------------------------------------
mkdir -p \
    storage/app/public \
    storage/app/whatsapp-sidecar/sessions \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

php artisan storage:link >/dev/null 2>&1 || true

# -----------------------------------------------------------------------------
# 4. Optimisasi & migrasi
# -----------------------------------------------------------------------------
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [[ "${RUN_MIGRATIONS}" == "true" ]]; then
    log "Menjalankan migrasi..."
    php artisan migrate --force
fi

# -----------------------------------------------------------------------------
# 5. Supervisor mini (loop restart otomatis)
# -----------------------------------------------------------------------------
PIDS=()

# Jalankan proses panjang dengan auto-restart.
# Output mengalir ke stdout (log deployment Railway) DAN file di storage/logs.
run_forever() {
    local name="$1"
    shift
    (
        # loop penjaga: kesalahan satu proses TIDAK boleh mematikan loop
        set +e
        while true; do
            "$@" > >(tee -a "storage/logs/${name}.log") 2>&1
            code=$?
            echo "[watchdog:${name}] proses keluar (kode ${code}), restart dalam 5 detik..."
            sleep 5
        done
    ) &
    PIDS+=("$!")
}

cleanup() {
    log "Menerima sinyal berhenti, mematikan semua proses anak..."
    local p
    for p in "${PIDS[@]:-}"; do
        kill "$p" 2>/dev/null || true
    done
    # Matikan sidecar dengan rapi lewat artisan (menulis ulang pid file)
    php artisan whatsapp:sidecar:stop >/dev/null 2>&1 || true
    exit 0
}
trap cleanup INT TERM

# -----------------------------------------------------------------------------
# 6. WhatsApp sidecar + watchdog terpisah
# -----------------------------------------------------------------------------
sidecar_alive() {
    [[ -f "${SIDECAR_PID_FILE}" ]] || return 1
    local pid
    pid="$(tr -d '[:space:]' <"${SIDECAR_PID_FILE}" 2>/dev/null)" || return 1
    [[ -n "${pid}" ]] || return 1
    kill -0 "${pid}" 2>/dev/null
}

start_sidecar() {
    log "Memulai WhatsApp sidecar..."
    php artisan whatsapp:sidecar:start || true

    (
        set +e
        while true; do
            sleep 15
            if ! sidecar_alive; then
                echo "[watchdog:sidecar] sidecar mati/belum jalan, memulai ulang..."
                php artisan whatsapp:sidecar:start || true
            fi
        done
    ) &
    PIDS+=("$!")
}

if [[ "${WHATSAPP_WEB_ENABLED:-true}" != "false" && "${START_SIDECAR}" != "false" ]]; then
    start_sidecar
    run_forever wa-listen php artisan whatsapp:web:listen "${WHATSAPP_SESSION_ID}"
else
    log "WhatsApp sidecar dinonaktifkan."
fi

# -----------------------------------------------------------------------------
# 7. Queue worker
# -----------------------------------------------------------------------------
if [[ "${START_QUEUE}" == "true" ]]; then
    run_forever queue php artisan queue:work --tries=3 --backoff=30 --timeout=120 --memory=256 --sleep=3
else
    log "Queue worker dinonaktifkan."
fi

# -----------------------------------------------------------------------------
# 8. Web server (foreground — container hidup selama proses ini hidup)
# -----------------------------------------------------------------------------
log "Menjalankan web server di 0.0.0.0:${PORT}"
php artisan serve --host=0.0.0.0 --port="${PORT}" &
SERVER_PID=$!
PIDS+=("${SERVER_PID}")

wait "${SERVER_PID}"
