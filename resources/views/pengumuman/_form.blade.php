<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-form.input label="Judul" name="judul" :value="$pengumuman->judul ?? ''" required />
    </div>

    <div class="sm:col-span-2">
        <x-form.textarea label="Konten" name="konten" :value="$pengumuman->konten ?? ''" rows="6" required />
    </div>

    <x-form.select label="Target" name="target_role" :selected="old('target_role', $pengumuman->target_role ?? 'semua')" :placeholder="false">
        @foreach (['semua' => 'Semua', 'admin' => 'Admin', 'guru' => 'Guru'] as $val => $label)
            <option value="{{ $val }}" @selected(old('target_role', $pengumuman->target_role ?? 'semua') === $val)>{{ $label }}</option>
        @endforeach
    </x-form.select>

    <x-form.input label="Tanggal Publish" name="tanggal_publish" type="date"
        :value="isset($pengumuman) ? optional($pengumuman->tanggal_publish)->format('Y-m-d') : ''" />

    <div class="sm:col-span-2">
        <x-form.checkbox label="Aktifkan pengumuman" name="is_active" :checked="old('is_active', $pengumuman->is_active ?? true)" />
    </div>
</div>

<div class="flex items-center gap-3 pt-6">
    <x-button type="submit" variant="primary"><x-icon name="check" class="h-4 w-4" /> Simpan</x-button>
    <x-button variant="secondary" :href="route('pengumuman.index')">Batal</x-button>
</div>
