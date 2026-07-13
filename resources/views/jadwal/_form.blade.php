@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Kelas</label>
        <select name="kelas_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}" @selected(old('kelas_id', $jadwal->kelas_id ?? null) == $k->id)>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Mata Pelajaran</label>
        <select name="mapel_id" class="form-select" required>
            <option value="">-- Pilih Mapel --</option>
            @foreach ($mapel as $m)
                <option value="{{ $m->id }}" @selected(old('mapel_id', $jadwal->mapel_id ?? null) == $m->id)>{{ $m->nama_mapel }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Guru</label>
        <select name="guru_id" class="form-select" required>
            <option value="">-- Pilih Guru --</option>
            @foreach ($guru as $g)
                <option value="{{ $g->id }}" @selected(old('guru_id', $jadwal->guru_id ?? null) == $g->id)>{{ $g->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Hari</label>
        <select name="hari" class="form-select" required>
            @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                <option value="{{ $h }}" @selected(old('hari', $jadwal->hari ?? '') === $h)>{{ $h }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Ke-</label>
        <input type="number" name="jam_ke" min="1" max="15" class="form-control" value="{{ old('jam_ke', $jadwal->jam_ke ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Mulai</label>
        <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', $jadwal->jam_mulai ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Selesai</label>
        <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', $jadwal->jam_selesai ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Ruangan</label>
        <input type="text" name="ruangan" class="form-control" value="{{ old('ruangan', $jadwal->ruangan ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran', $jadwal->tahun_ajaran ?? '') }}" placeholder="2025/2026" required>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Batal</a>
</div>
