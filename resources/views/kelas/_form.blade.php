@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Kelas</label>
        <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tingkat</label>
        <input type="text" name="tingkat" class="form-control" value="{{ old('tingkat', $kelas->tingkat ?? '') }}" placeholder="X / XI / XII" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jenjang</label>
        <select name="jenjang" class="form-select" required>
            @foreach (['SD','SMP','SMA','SMK'] as $j)
                <option value="{{ $j }}" @selected(old('jenjang', $kelas->jenjang ?? '') === $j)>{{ $j }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" value="{{ old('tahun_ajaran', $kelas->tahun_ajaran ?? '') }}" placeholder="2025/2026" required>
    </div>
    <div class="col-md-5">
        <label class="form-label">Wali Kelas</label>
        <select name="wali_kelas_id" class="form-select">
            <option value="">-- Tidak ada --</option>
            @foreach ($guru as $g)
                <option value="{{ $g->id }}" @selected(old('wali_kelas_id', $kelas->wali_kelas_id ?? null) == $g->id)>{{ $g->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Kapasitas</label>
        <input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', $kelas->kapasitas ?? '') }}">
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
</div>
