@csrf
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">NIS</label>
        <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis ?? '') }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $siswa->nama_lengkap ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kelas</label>
        <select name="kelas_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}" @selected(old('kelas_id', $siswa->kelas_id ?? null) == $k->id)>{{ $k->nama_kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', optional($siswa->tanggal_lahir ?? null)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select">
            <option value="">--</option>
            <option value="L" @selected(old('jenis_kelamin', $siswa->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('jenis_kelamin', $siswa->jenis_kelamin ?? '') === 'P')>Perempuan</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun Masuk</label>
        <input type="number" name="tahun_masuk" class="form-control" value="{{ old('tahun_masuk', $siswa->tahun_masuk ?? date('Y')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach (['Aktif','Lulus','Pindah','Keluar'] as $st)
                <option value="{{ $st }}" @selected(old('status', $siswa->status ?? 'Aktif') === $st)>{{ $st }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
    </div>
</div>
<div class="mt-4">
    <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
    <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
</div>
