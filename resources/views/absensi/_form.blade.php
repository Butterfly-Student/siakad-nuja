@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Siswa</label>
        <select name="siswa_id" class="form-select" required>
            <option value="">-- Pilih Siswa --</option>
            @foreach ($siswa as $s)
                <option value="{{ $s->id }}" @selected(old('siswa_id', $absensi->siswa_id ?? null) == $s->id)>{{ $s->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Jadwal</label>
        <select name="jadwal_id" class="form-select" required>
            <option value="">-- Pilih Jadwal --</option>
            @foreach ($jadwal as $j)
                <option value="{{ $j->id }}" @selected(old('jadwal_id', $absensi->jadwal_id ?? null) == $j->id)>
                    {{ $j->hari }} — {{ $j->mapel->nama_mapel ?? '-' }} ({{ $j->kelas->nama_kelas ?? '-' }})
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', optional($absensi->tanggal ?? null)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach (['Hadir','Izin','Sakit','Alpa'] as $st)
                <option value="{{ $st }}" @selected(old('status', $absensi->status ?? 'Hadir') === $st)>{{ $st }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $absensi->keterangan ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">Batal</a>
</div>
