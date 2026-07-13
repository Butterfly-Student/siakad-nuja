@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru->nip ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $guru->nama_lengkap ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $guru->user->email ?? '') }}" required>
    </div>
    @if (!isset($guru))
        <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
    @endif
    <div class="col-md-6">
        <label class="form-label">Jabatan</label>
        <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $guru->jabatan ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">No HP</label>
        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp ?? '') }}">
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('guru.index') }}" class="btn btn-secondary">Batal</a>
</div>
