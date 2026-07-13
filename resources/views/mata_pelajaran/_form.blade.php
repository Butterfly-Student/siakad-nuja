@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Kode Mapel</label>
        <input type="text" name="kode_mapel" class="form-control" value="{{ old('kode_mapel', $mapel->kode_mapel ?? '') }}" required>
    </div>
    <div class="col-md-5">
        <label class="form-label">Nama Mapel</label>
        <input type="text" name="nama_mapel" class="form-control" value="{{ old('nama_mapel', $mapel->nama_mapel ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jenjang</label>
        <select name="jenjang" class="form-select" required>
            @foreach (['SD','SMP','SMA','SMK'] as $j)
                <option value="{{ $j }}" @selected(old('jenjang', $mapel->jenjang ?? '') === $j)>{{ $j }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">KKM</label>
        <input type="number" name="kkm" class="form-control" value="{{ old('kkm', $mapel->kkm ?? '') }}">
    </div>
    <div class="col-md-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $mapel->deskripsi ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('mata-pelajaran.index') }}" class="btn btn-secondary">Batal</a>
</div>
