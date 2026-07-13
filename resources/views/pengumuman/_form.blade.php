@csrf
<div class="row g-3">
    <div class="col-md-12">
        <label class="form-label">Judul</label>
        <input type="text" name="judul" class="form-control" value="{{ old('judul', $pengumuman->judul ?? '') }}" required>
    </div>
    <div class="col-md-12">
        <label class="form-label">Konten</label>
        <textarea name="konten" class="form-control" rows="6" required>{{ old('konten', $pengumuman->konten ?? '') }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Target Role</label>
        <select name="target_role" class="form-select">
            <option value="">Semua</option>
            @foreach (['admin','guru','siswa','orang_tua'] as $r)
                <option value="{{ $r }}" @selected(old('target_role', $pengumuman->target_role ?? '') === $r)>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tanggal Publish</label>
        <input type="date" name="tanggal_publish" class="form-control" value="{{ old('tanggal_publish', optional($pengumuman->tanggal_publish ?? null)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="isActive"
                {{ old('is_active', ($pengumuman->is_active ?? true)) ? 'checked' : '' }}>
            <label for="isActive" class="form-check-label">Aktif</label>
        </div>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">Batal</a>
</div>
