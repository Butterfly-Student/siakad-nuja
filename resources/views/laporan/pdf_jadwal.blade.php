<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        .header-title h2 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h1 {
            margin: 2px 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
        }

        table.jadwal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }
        table.jadwal-table th, table.jadwal-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
            word-wrap: break-word;
        }
        table.jadwal-table th {
            background-color: #EAEAEA;
            font-weight: bold;
            text-transform: uppercase;
        }
        .col-no { width: 35px; }
        .col-hari { width: 75px; }
        .col-jam { width: 45px; }
        .col-pukul { width: 95px; }

        .bg-kegiatan {
            background-color: #F8F9FA;
            font-weight: bold;
            text-align: left !important;
            padding-left: 10px !important;
            font-style: italic;
        }
        .text-mapel {
            font-weight: bold;
            font-size: 8.5pt;
            display: block;
        }
        .text-guru {
            font-size: 8pt;
            color: #222;
            display: block;
            margin-top: 2px;
        }

        .footer-signature {
            margin-top: 25px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 10pt;
        }
        .signature-space {
            height: 55px;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header-title">
        <h2>JADWAL PELAJARAN</h2>
        <h1>{{ $jenjang === 'MTs' ? "MADRASAH TSANAWIYAH AS-SYAFI'IE" : "MADRASAH IBTIDAIYAH NURUL JADID" }}</h1>
        <p>KARDULUK PRAGAAN SUMENEP 69465 TAPEL 2026/2027</p>
    </div>

    <table class="jadwal-table">
        <thead>
            @if($tipe === 'keseluruhan')
                <tr>
                    <th rowspan="2" class="col-no">NO</th>
                    <th rowspan="2" class="col-hari">HARI</th>
                    <th rowspan="2" class="col-jam">JAM</th>
                    <th rowspan="2" class="col-pukul">PUKUL</th>
                    <th colspan="{{ count($kelas) }}">KELAS / MATA PELAJARAN / GURU</th>
                </tr>
                <tr>
                    @foreach($kelas as $k)
                        <th>{{ $k->nama_kelas }}</th>
                    @endforeach
                </tr>
            @elseif($tipe === 'per_kelas')
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-hari">HARI</th>
                    <th class="col-jam">JAM</th>
                    <th class="col-pukul">PUKUL</th>
                    <th>MATA PELAJARAN</th>
                    <th>GURU PENGAMPU</th>
                </tr>
            @elseif($tipe === 'per_guru')
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-hari">HARI</th>
                    <th class="col-jam">JAM</th>
                    <th class="col-pukul">PUKUL</th>
                    <th>KELAS</th>
                    <th>MATA PELAJARAN</th>
                    <th>RUANGAN</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($hariList as $hIndex => $hari)
                @php
                    $keg = $waktuKegiatan[$hari] ?? null;
                    $colSpanAll = $tipe === 'keseluruhan' ? count($kelas) : ($tipe === 'per_kelas' ? 2 : 3);
                @endphp

                <!-- Row Kegiatan / Pembiasaan -->
                @if($keg)
                    <tr>
                        <td>{{ $hIndex + 1 }}</td>
                        <td style="font-weight: bold;">{{ strtoupper($hari) }}</td>
                        <td>{{ $keg['jam'] }}</td>
                        <td>{{ $keg['pukul'] }}</td>
                        <td colspan="{{ $colSpanAll }}" class="bg-kegiatan">
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
                        <!-- Baris Mata Pelajaran -->
                        <tr>
                            @if($jam === 1 && !$keg)
                                <td rowspan="6">{{ $hIndex + 1 }}</td>
                                <td rowspan="6" style="font-weight: bold;">{{ strtoupper($hari) }}</td>
                            @elseif($jam === 1)
                                <!-- Hari sudah ditampilkan di baris kegiatan -->
                                <td></td>
                                <td></td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                            <td>{{ $jam }}</td>
                            <td>{{ $pukulSlot }}</td>
                            @foreach($kelas as $k)
                                @php
                                    $cell = $matrix[$hari][$jam][$k->id] ?? null;
                                @endphp
                                <td>
                                    @if($cell)
                                        <span class="text-mapel">{{ $cell['mapel'] }}</span>
                                        <span class="text-guru">{{ $cell['guru'] }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @elseif($tipe === 'per_kelas')
                        @php
                            $cell = $matrix[$hari][$jam] ?? null;
                        @endphp
                        <tr>
                            @if($jam === 1 && !$keg)
                                <td>{{ $hIndex + 1 }}</td>
                                <td style="font-weight: bold;">{{ strtoupper($hari) }}</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                            <td>{{ $jam }}</td>
                            <td>{{ $pukulSlot }}</td>
                            <td style="font-weight: bold;">{{ $cell['mapel'] ?? '-' }}</td>
                            <td>{{ $cell['guru'] ?? '-' }}</td>
                        </tr>
                    @elseif($tipe === 'per_guru')
                        @php
                            $cells = $matrix[$hari][$jam] ?? [];
                        @endphp
                        @if(count($cells) > 0)
                            @foreach($cells as $cIndex => $c)
                                <tr>
                                    @if($jam === 1 && $cIndex === 0 && !$keg)
                                        <td>{{ $hIndex + 1 }}</td>
                                        <td style="font-weight: bold;">{{ strtoupper($hari) }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif
                                    <td>{{ $jam }}</td>
                                    <td>{{ $pukulSlot }}</td>
                                    <td style="font-weight: bold;">Kelas {{ $c['kelas'] }} ({{ $c['jenjang'] }})</td>
                                    <td>{{ $c['mapel'] }}</td>
                                    <td>{{ $c['ruangan'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td></td>
                                <td></td>
                                <td>{{ $jam }}</td>
                                <td>{{ $pukulSlot }}</td>
                                <td colspan="3" style="color: #888;">- Tidak Ada Jam Mengajar -</td>
                            </tr>
                        @endif
                    @endif
                @endfor
            @endforeach
        </tbody>
    </table>

    <div class="footer-signature">
        <div class="signature-box">
            <p>Sumenep, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p style="font-weight: bold; margin-top: -5px;">Kepala Madrasah,</p>
            <div class="signature-space"></div>
            <p style="font-weight: bold; text-decoration: underline;">
                {{ $jenjang === 'MTs' ? 'Hafi, M.Pd.' : 'ABD. KAFI, S.Pd.I' }}
            </p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
