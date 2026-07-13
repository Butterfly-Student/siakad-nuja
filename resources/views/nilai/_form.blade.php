@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Siswa</label>
        <select name="siswa_id" class="form-select" required>
            <option value="">-- Pilih Siswa --</option>
            @foreach ($siswa as $s)
                <option value="{{ $s->id }}" @selected(old('siswa_id', $nilai->siswa_id ?? null) == $s->id)>{{ $s->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Mata Pelajaran</label>
        <select name="mapel_id" class="form-select" required>
            <option value="">-- Pilih Mapel --</option>
            @foreach ($mapel as $m)
                <option value="{{ $m->id }}" @selected(old('mapel_id', $nilai->mapel_id ?? null) == $m->id)>{{ $m->nama_mapel }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kelas</label>
        <select name="kelas_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}" @selected(old('kelas_id', $nilai->kelas_id ?? null) == $k->id)>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-select" required>
            @foreach (['Ganjil','Genap'] as $sem)
                <option value="{{ $sem }}" @selected(old('semester', $nilai->semester ?? '') === $sem)>{{ $sem }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran', $nilai->tahun_ajaran ?? '') }}" placeholder="2025/2026" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Nilai Harian</label>
        <input type="number" step="0.01" name="nilai_harian" class="form-control" value="{{ old('nilai_harian', $nilai->nilai_harian ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Nilai UTS</label>
        <input type="number" step="0.01" name="nilai_uts" class="form-control" value="{{ old('nilai_uts', $nilai->nilai_uts ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Nilai UAS</label>
        <input type="number" step="0.01" name="nilai_uas" class="form-control" value="{{ old('nilai_uas', $nilai->nilai_uas ?? '') }}">
    </div>
</div>
<div class="alert alert-info small mt-3">
    Nilai akhir & predikat akan dihitung otomatis. Bobot: Harian 30%, UTS 30%, UAS 40%.
</div>
<div class="mt-2">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Batal</a>
</div>
