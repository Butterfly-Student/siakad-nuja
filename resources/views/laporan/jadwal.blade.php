@extends('layouts.app')

@section('title', $title)
@section('header', 'Preview Jadwal Pelajaran')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $title }}</h2>
            <p class="text-sm text-slate-500">Preview Jadwal Pelajaran SIAKAD Nurul Jadid (TAPEL 2026/2027)</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('laporan.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition">
                Kembali
            </a>
            <a href="{{ route('laporan.jadwal', ['tipe' => $tipe, 'jenjang' => $jenjang, 'kelas_id' => $selectedKelas?->id, 'guru_id' => $selectedGuru?->id, 'export' => 'pdf']) }}" class="px-4 py-2.5 rounded-xl bg-amber-600 text-white font-semibold hover:bg-amber-700 transition shadow-sm flex items-center gap-2">
                <x-icon name="download" class="h-4 w-4" /> Download PDF
            </a>
            <a href="{{ route('laporan.jadwal', ['tipe' => $tipe, 'jenjang' => $jenjang, 'kelas_id' => $selectedKelas?->id, 'guru_id' => $selectedGuru?->id, 'export' => 'excel']) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition shadow-sm flex items-center gap-2">
                <x-icon name="download" class="h-4 w-4" /> Download Excel
            </a>
        </div>
    </div>

    <!-- Table Sheet Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 overflow-x-auto">
        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-slate-800 tracking-wide uppercase">JADWAL PELAJARAN</h3>
            <h2 class="text-xl font-extrabold text-slate-900 uppercase">
                {{ $jenjang === 'MTs' ? "MADRASAH TSANAWIYAH AS-SYAFI'IE" : "MADRASAH IBTIDAIYAH NURUL JADID" }}
            </h2>
            <p class="text-sm font-semibold text-slate-600">KARDULUK PRAGAAN SUMENEP 69465 TAPEL 2026/2027</p>
        </div>

        <table class="w-full text-sm border-collapse border border-slate-800 text-center">
            <thead>
                @if($tipe === 'keseluruhan')
                    <tr class="bg-slate-100 font-bold border-b border-slate-800 text-slate-900">
                        <th rowspan="2" class="border border-slate-800 px-3 py-2 w-12">NO</th>
                        <th rowspan="2" class="border border-slate-800 px-3 py-2 w-24">HARI</th>
                        <th rowspan="2" class="border border-slate-800 px-3 py-2 w-16">JAM</th>
                        <th rowspan="2" class="border border-slate-800 px-3 py-2 w-32">PUKUL</th>
                        <th colspan="{{ count($kelas) }}" class="border border-slate-800 px-3 py-2">KELAS / MATA PELAJARAN / GURU</th>
                    </tr>
                    <tr class="bg-slate-100 font-bold border-b border-slate-800 text-slate-900">
                        @foreach($kelas as $k)
                            <th class="border border-slate-800 px-3 py-2">Kelas {{ $k->nama_kelas }}</th>
                        @endforeach
                    </tr>
                @elseif($tipe === 'per_kelas')
                    <tr class="bg-slate-100 font-bold border-b border-slate-800 text-slate-900">
                        <th class="border border-slate-800 px-3 py-2 w-12">NO</th>
                        <th class="border border-slate-800 px-3 py-2 w-24">HARI</th>
                        <th class="border border-slate-800 px-3 py-2 w-16">JAM</th>
                        <th class="border border-slate-800 px-3 py-2 w-32">PUKUL</th>
                        <th class="border border-slate-800 px-3 py-2">MATA PELAJARAN</th>
                        <th class="border border-slate-800 px-3 py-2">GURU PENGAMPU</th>
                    </tr>
                @elseif($tipe === 'per_guru')
                    <tr class="bg-slate-100 font-bold border-b border-slate-800 text-slate-900">
                        <th class="border border-slate-800 px-3 py-2 w-12">NO</th>
                        <th class="border border-slate-800 px-3 py-2 w-24">HARI</th>
                        <th class="border border-slate-800 px-3 py-2 w-16">JAM</th>
                        <th class="border border-slate-800 px-3 py-2 w-32">PUKUL</th>
                        <th class="border border-slate-800 px-3 py-2">KELAS</th>
                        <th class="border border-slate-800 px-3 py-2">MATA PELAJARAN</th>
                        <th class="border border-slate-800 px-3 py-2">RUANGAN</th>
                    </tr>
                @endif
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($hariList as $hIndex => $hari)
                    @php
                        $keg = $waktuKegiatan[$hari] ?? null;
                        $colSpanAll = $tipe === 'keseluruhan' ? count($kelas) : ($tipe === 'per_kelas' ? 2 : 3);
                    @endphp

                    <!-- Kegiatan / Pembiasaan -->
                    @if($keg)
                        <tr class="bg-slate-50">
                            <td class="border border-slate-800 px-2 py-1.5 font-medium">{{ $hIndex + 1 }}</td>
                            <td class="border border-slate-800 px-2 py-1.5 font-bold uppercase">{{ $hari }}</td>
                            <td class="border border-slate-800 px-2 py-1.5 font-semibold text-slate-700">{{ $keg['jam'] }}</td>
                            <td class="border border-slate-800 px-2 py-1.5 text-xs font-semibold text-slate-700">{{ $keg['pukul'] }}</td>
                            <td colspan="{{ $colSpanAll }}" class="border border-slate-800 px-4 py-1.5 text-left italic font-semibold text-slate-700">
                                {{ $keg['kegiatan'] }}
                            </td>
                        </tr>
                    @endif

                    <!-- Slot Jam 1, 2, 3 -->
                    @for($jam = 1; $jam <= 3; $jam++)
                        @php
                            $pukulSlot = $jam === 1 ? '07.30 - 08.40' : ($jam === 2 ? '09.10 - 10.20' : '10.20 - 11.25');
                        @endphp

                        @if($tipe === 'keseluruhan')
                            <tr>
                                <td class="border border-slate-800 px-2 py-1.5"></td>
                                <td class="border border-slate-800 px-2 py-1.5 font-bold uppercase">
                                    @if($jam === 1 && !$keg) {{ $hari }} @endif
                                </td>
                                <td class="border border-slate-800 px-2 py-1.5 font-semibold">{{ $jam }}</td>
                                <td class="border border-slate-800 px-2 py-1.5 text-xs font-semibold">{{ $pukulSlot }}</td>
                                @foreach($kelas as $k)
                                    @php
                                        $cell = $matrix[$hari][$jam][$k->id] ?? null;
                                    @endphp
                                    <td class="border border-slate-800 px-2 py-1.5">
                                        @if($cell)
                                            <span class="block font-bold text-slate-900 text-xs">{{ $cell['mapel'] }}</span>
                                            <span class="block text-slate-600 text-[11px] mt-0.5">{{ $cell['guru'] }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @elseif($tipe === 'per_kelas')
                            @php
                                $cell = $matrix[$hari][$jam] ?? null;
                            @endphp
                            <tr>
                                <td class="border border-slate-800 px-2 py-1.5"></td>
                                <td class="border border-slate-800 px-2 py-1.5 font-bold uppercase">
                                    @if($jam === 1 && !$keg) {{ $hari }} @endif
                                </td>
                                <td class="border border-slate-800 px-2 py-1.5 font-semibold">{{ $jam }}</td>
                                <td class="border border-slate-800 px-2 py-1.5 text-xs font-semibold">{{ $pukulSlot }}</td>
                                <td class="border border-slate-800 px-3 py-1.5 font-bold text-slate-900">{{ $cell['mapel'] ?? '-' }}</td>
                                <td class="border border-slate-800 px-3 py-1.5 text-slate-700">{{ $cell['guru'] ?? '-' }}</td>
                            </tr>
                        @elseif($tipe === 'per_guru')
                            @php
                                $cells = $matrix[$hari][$jam] ?? [];
                            @endphp
                            @if(count($cells) > 0)
                                @foreach($cells as $cIndex => $c)
                                    <tr>
                                        <td class="border border-slate-800 px-2 py-1.5"></td>
                                        <td class="border border-slate-800 px-2 py-1.5 font-bold uppercase">
                                            @if($jam === 1 && $cIndex === 0 && !$keg) {{ $hari }} @endif
                                        </td>
                                        <td class="border border-slate-800 px-2 py-1.5 font-semibold">{{ $jam }}</td>
                                        <td class="border border-slate-800 px-2 py-1.5 text-xs font-semibold">{{ $pukulSlot }}</td>
                                        <td class="border border-slate-800 px-3 py-1.5 font-bold text-slate-900">Kelas {{ $c['kelas'] }} ({{ $c['jenjang'] }})</td>
                                        <td class="border border-slate-800 px-3 py-1.5 font-medium text-slate-800">{{ $c['mapel'] }}</td>
                                        <td class="border border-slate-800 px-3 py-1.5 text-slate-600">{{ $c['ruangan'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="border border-slate-800 px-2 py-1.5"></td>
                                    <td class="border border-slate-800 px-2 py-1.5 font-bold uppercase">
                                        @if($jam === 1 && !$keg) {{ $hari }} @endif
                                    </td>
                                    <td class="border border-slate-800 px-2 py-1.5 font-semibold">{{ $jam }}</td>
                                    <td class="border border-slate-800 px-2 py-1.5 text-xs font-semibold">{{ $pukulSlot }}</td>
                                    <td colspan="3" class="border border-slate-800 px-3 py-1.5 text-slate-400 italic">- Tidak Ada Jam Mengajar -</td>
                                </tr>
                            @endif
                        @endif
                    @endfor
                @endforeach
            </tbody>
        </table>

        <!-- Signature Footer -->
        <div class="mt-8 flex justify-end">
            <div class="text-center w-64">
                <p class="text-sm text-slate-700">Sumenep, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="text-sm font-bold text-slate-900 mt-0.5">Kepala Madrasah,</p>
                <div class="h-16"></div>
                <p class="text-sm font-bold text-slate-900 underline">
                    {{ $jenjang === 'MTs' ? 'Hafi, M.Pd.' : 'ABD. KAFI, S.Pd.I' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
