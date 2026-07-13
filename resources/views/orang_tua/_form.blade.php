@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Siswa</label>
        <select name="siswa_id" class="form-select" required>
            <option value="">-- Pilih Siswa --</option>
            @foreach ($siswa as $s)
                <option value="{{ $s->id }}" @selected(old('siswa_id', $orangTua->siswa_id ?? null) == $s->id)>{{ $s->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" value="{{ old('nama', $orangTua->nama ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Hubungan</label>
        <select name="hubungan" class="form-select">
            <option value="">--</option>
            @foreach (['Ayah','Ibu','Wali'] as $h)
                <option value="{{ $h }}" @selected(old('hubungan', $orangTua->hubungan ?? '') === $h)>{{ $h }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">No HP</label>
        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $orangTua->no_hp ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Pekerjaan</label>
        <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $orangTua->pekerjaan ?? '') }}">
    </div>
    <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $orangTua->alamat ?? '') }}</textarea>
    </div>
    <div class="col-md-12 form-check ms-3">
        <input type="hidden" name="is_kontak_utama" value="0">
        <input type="checkbox" name="is_kontak_utama" value="1" class="form-check-input" id="kontakUtama"
            {{ old('is_kontak_utama', $orangTua->is_kontak_utama ?? false) ? 'checked' : '' }}>
        <label for="kontakUtama" class="form-check-label">Jadikan kontak utama</label>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('orang-tua.index') }}" class="btn btn-secondary">Batal</a>
</div>
