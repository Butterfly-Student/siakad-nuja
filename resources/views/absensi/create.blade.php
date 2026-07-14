@extends('layouts.app')

@section('title', 'Entri Absensi')

@section('content')
<x-page-header title="Entri Absensi" subtitle="Pilih jadwal dan tanggal untuk mengisi kehadiran seluruh siswa." />

<x-card>
    @if ($jadwal->isNotEmpty())
        <form method="GET" action="{{ route('absensi.roster') }}" class="space-y-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-form.select label="Jadwal" name="jadwal_id" :selected="request('jadwal_id')" required>
                    @foreach ($jadwal as $j)
                        <option value="{{ $j->id }}" @selected(request('jadwal_id') == $j->id)>
                            {{ $j->kelas->nama_kelas ?? '-' }} — {{ $j->mapel->nama_mapel ?? '-' }} ({{ $j->hari }}, jam ke-{{ $j->jam_ke }})
                        </option>
                    @endforeach
                </x-form.select>

                <x-form.input label="Tanggal" name="tanggal" type="date"
                    :value="request('tanggal', now()->format('Y-m-d'))" required />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">
                    <x-icon name="absensi" class="h-4 w-4" /> Tampilkan Siswa
                </x-button>
                <x-button variant="secondary" :href="route('absensi.index')">Batal</x-button>
            </div>
        </form>
    @else
        <x-empty-state icon="jadwal" title="Tidak ada jadwal tersedia"
            description="Anda belum memiliki jadwal mengajar. Hubungi administrator." />
    @endif
</x-card>
@endsection
