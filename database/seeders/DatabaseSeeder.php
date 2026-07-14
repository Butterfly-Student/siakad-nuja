<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\OrangTua;
use App\Models\Pembayaran;
use App\Models\Pengumuman;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private const TAHUN_AJARAN = '2024/2025';

    public function run(): void
    {
        $admin = $this->seedAdmin();
        $guruList = $this->seedGuru();
        $mapelList = $this->seedMataPelajaran();
        $kelasList = $this->seedKelas($guruList);
        $siswaList = $this->seedSiswa($kelasList);
        $this->seedOrangTua($siswaList);
        $jadwalList = $this->seedJadwal($kelasList, $mapelList, $guruList);
        $this->seedNilai($siswaList, $mapelList);
        $this->seedAbsensi($jadwalList, $siswaList);
        $this->seedPengumuman($admin);
        $this->seedTagihan($siswaList, $admin);

        $this->command->info('Seeding selesai. Login admin: admin@siakadnuja.sch.id / password');
    }

    private function seedAdmin(): User
    {
        return User::create([
            'nama' => 'Administrator',
            'email' => 'admin@siakadnuja.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'no_hp' => '081200000001',
            'is_active' => true,
        ]);
    }

    /**
     * @return array<int, Guru>
     */
    private function seedGuru(): array
    {
        $namaGuru = [
            'Ahmad Fauzi, S.Pd.', 'Siti Aminah, S.Pd.', 'Muhammad Rizki, S.Pd.',
            'Dewi Lestari, S.Pd.', 'Bambang Sutrisno, S.Pd.', 'Nur Halimah, S.Ag.',
            'Joko Prasetyo, S.Pd.', 'Rina Marlina, S.Pd.', 'Hendra Gunawan, S.Kom.',
            'Fatimah Zahra, S.Pd.',
        ];

        $jabatan = ['Guru Mata Pelajaran', 'Guru & Wali Kelas', 'Guru Senior', 'Kepala Lab'];
        $guruList = [];

        foreach ($namaGuru as $i => $nama) {
            $urut = $i + 1;
            $user = User::create([
                'nama' => $nama,
                'email' => 'guru' . $urut . '@siakadnuja.sch.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_GURU,
                'no_hp' => '08130000' . str_pad((string) $urut, 4, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $guruList[] = Guru::create([
                'user_id' => $user->id,
                'nip' => '1985' . str_pad((string) ($urut * 7), 8, '0', STR_PAD_LEFT),
                'nama_lengkap' => $nama,
                'jabatan' => $jabatan[$i % count($jabatan)],
                'no_hp' => $user->no_hp,
            ]);
        }

        return $guruList;
    }

    /**
     * @return array<int, MataPelajaran>
     */
    private function seedMataPelajaran(): array
    {
        $mapel = [
            ['MTK', 'Matematika', 75],
            ['BIN', 'Bahasa Indonesia', 75],
            ['BIG', 'Bahasa Inggris', 72],
            ['IPA', 'Ilmu Pengetahuan Alam', 73],
            ['IPS', 'Ilmu Pengetahuan Sosial', 73],
            ['PKN', 'Pendidikan Kewarganegaraan', 75],
            ['PAI', 'Pendidikan Agama Islam', 75],
            ['PJK', 'Pendidikan Jasmani & Kesehatan', 78],
            ['SBD', 'Seni Budaya', 76],
            ['TIK', 'Teknologi Informasi & Komunikasi', 75],
        ];

        $list = [];
        foreach ($mapel as [$kode, $nama, $kkm]) {
            $list[] = MataPelajaran::create([
                'kode_mapel' => $kode,
                'nama_mapel' => $nama,
                'jenjang' => 'SMP',
                'kkm' => $kkm,
                'deskripsi' => 'Mata pelajaran ' . $nama . ' untuk jenjang SMP.',
            ]);
        }

        return $list;
    }

    /**
     * @param  array<int, Guru>  $guruList
     * @return array<int, Kelas>
     */
    private function seedKelas(array $guruList): array
    {
        $kelasNama = [
            ['7A', '7'], ['7B', '7'],
            ['8A', '8'], ['8B', '8'],
            ['9A', '9'], ['9B', '9'],
        ];

        $list = [];
        foreach ($kelasNama as $i => [$nama, $tingkat]) {
            $list[] = Kelas::create([
                'nama_kelas' => $nama,
                'tingkat' => $tingkat,
                'jenjang' => 'SMP',
                'tahun_ajaran' => self::TAHUN_AJARAN,
                'wali_kelas_id' => $guruList[$i]->id, // 6 kelas ↔ 6 guru pertama sebagai wali
                'kapasitas' => 32,
            ]);
        }

        return $list;
    }

    /**
     * @param  array<int, Kelas>  $kelasList
     * @return array<int, Siswa>
     */
    private function seedSiswa(array $kelasList): array
    {
        $all = [];
        foreach ($kelasList as $kelas) {
            $siswaKelas = Siswa::factory()->count(28)->create(['kelas_id' => $kelas->id]);
            foreach ($siswaKelas as $s) {
                $all[] = $s;
            }
        }

        return $all;
    }

    /**
     * @param  array<int, Siswa>  $siswaList
     */
    private function seedOrangTua(array $siswaList): void
    {
        foreach ($siswaList as $siswa) {
            OrangTua::factory()->create([
                'siswa_id' => $siswa->id,
                'hubungan' => 'Ayah',
                'is_kontak_utama' => true,
            ]);
            OrangTua::factory()->create([
                'siswa_id' => $siswa->id,
                'hubungan' => 'Ibu',
                'is_kontak_utama' => false,
            ]);
        }
    }

    /**
     * Jadwal koheren: tiap kelas 5 hari x 6 jam, tanpa bentrok guru pada slot yang sama.
     *
     * @param  array<int, Kelas>  $kelasList
     * @param  array<int, MataPelajaran>  $mapelList
     * @param  array<int, Guru>  $guruList
     * @return array<int, JadwalPelajaran>
     */
    private function seedJadwal(array $kelasList, array $mapelList, array $guruList): array
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $slot = [
            1 => ['07:00', '07:40'],
            2 => ['07:40', '08:20'],
            3 => ['08:20', '09:00'],
            4 => ['09:15', '09:55'],
            5 => ['09:55', '10:35'],
            6 => ['10:35', '11:15'],
        ];

        // Tiap mapel diampu satu guru tetap.
        $mapelGuru = [];
        foreach ($mapelList as $i => $mapel) {
            $mapelGuru[$mapel->id] = $guruList[$i % count($guruList)];
        }

        $jadwalList = [];
        $guruSlot = []; // [hari][jam_ke][guru_id] => true

        foreach ($kelasList as $kelas) {
            $mapelIndex = 0;
            foreach ($hariList as $hari) {
                foreach ($slot as $jamKe => [$mulai, $selesai]) {
                    $mapel = $mapelList[$mapelIndex % count($mapelList)];
                    $guru = $mapelGuru[$mapel->id];

                    $tries = 0;
                    while (isset($guruSlot[$hari][$jamKe][$guru->id]) && $tries < count($mapelList)) {
                        $mapelIndex++;
                        $mapel = $mapelList[$mapelIndex % count($mapelList)];
                        $guru = $mapelGuru[$mapel->id];
                        $tries++;
                    }

                    $guruSlot[$hari][$jamKe][$guru->id] = true;

                    $jadwalList[] = JadwalPelajaran::create([
                        'kelas_id' => $kelas->id,
                        'mapel_id' => $mapel->id,
                        'guru_id' => $guru->id,
                        'hari' => $hari,
                        'jam_ke' => $jamKe,
                        'jam_mulai' => $mulai,
                        'jam_selesai' => $selesai,
                        'ruangan' => 'R-' . $kelas->nama_kelas,
                        'tahun_ajaran' => self::TAHUN_AJARAN,
                    ]);

                    $mapelIndex++;
                }
            }
        }

        return $jadwalList;
    }

    /**
     * Nilai per siswa untuk 6 mapel inti; nilai_akhir & predikat terhitung.
     *
     * @param  array<int, Siswa>  $siswaList
     * @param  array<int, MataPelajaran>  $mapelList
     */
    private function seedNilai(array $siswaList, array $mapelList): void
    {
        $mapelInti = array_slice($mapelList, 0, 6);
        $rows = [];

        foreach ($siswaList as $siswa) {
            foreach ($mapelInti as $mapel) {
                $harian = fake()->numberBetween(65, 95);
                $uts = fake()->numberBetween(60, 95);
                $uas = fake()->numberBetween(60, 98);
                $akhir = Nilai::hitungNilaiAkhir($harian, $uts, $uas);

                $rows[] = [
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $mapel->id,
                    'kelas_id' => $siswa->kelas_id,
                    'semester' => 'Ganjil',
                    'tahun_ajaran' => self::TAHUN_AJARAN,
                    'nilai_harian' => $harian,
                    'nilai_uts' => $uts,
                    'nilai_uas' => $uas,
                    'nilai_akhir' => $akhir,
                    'predikat' => Nilai::hitungPredikat($akhir),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Nilai::insert($chunk);
        }
    }

    /**
     * Absensi 5 hari kerja terakhir untuk jam pertama tiap kelas.
     *
     * @param  array<int, JadwalPelajaran>  $jadwalList
     * @param  array<int, Siswa>  $siswaList
     */
    private function seedAbsensi(array $jadwalList, array $siswaList): void
    {
        $jadwalPerKelas = [];
        foreach ($jadwalList as $jadwal) {
            if ($jadwal->jam_ke === 1 && ! isset($jadwalPerKelas[$jadwal->kelas_id])) {
                $jadwalPerKelas[$jadwal->kelas_id] = $jadwal;
            }
        }

        $siswaPerKelas = [];
        foreach ($siswaList as $siswa) {
            $siswaPerKelas[$siswa->kelas_id][] = $siswa;
        }

        $tanggalList = [];
        for ($i = 1; $i <= 5; $i++) {
            $tanggalList[] = now()->subDays($i)->format('Y-m-d');
        }

        $rows = [];
        foreach ($jadwalPerKelas as $kelasId => $jadwal) {
            foreach ($tanggalList as $tanggal) {
                foreach ($siswaPerKelas[$kelasId] ?? [] as $siswa) {
                    $status = fake()->randomElement(['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Izin', 'Sakit', 'Alpa']);
                    $rows[] = [
                        'siswa_id' => $siswa->id,
                        'jadwal_id' => $jadwal->id,
                        'tanggal' => $tanggal,
                        'status' => $status,
                        'keterangan' => $status === 'Izin' ? 'Ada keperluan keluarga' : null,
                        'created_at' => now(),
                    ];
                }
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            Absensi::insert($chunk);
        }
    }

    private function seedPengumuman(User $admin): void
    {
        $data = [
            ['Penerimaan Rapor Semester Ganjil', 'Pembagian rapor semester ganjil akan dilaksanakan pada hari Sabtu. Mohon kehadiran orang tua/wali murid.', 'semua'],
            ['Rapat Koordinasi Guru', 'Seluruh guru diharapkan hadir dalam rapat koordinasi persiapan ujian akhir semester di ruang guru.', 'guru'],
            ['Libur Semester', 'Libur semester ganjil dimulai setelah pembagian rapor. Kegiatan belajar dimulai kembali sesuai kalender akademik.', 'semua'],
        ];

        foreach ($data as $i => [$judul, $konten, $target]) {
            Pengumuman::create([
                'judul'           => $judul,
                'konten'          => $konten,
                'target_role'     => $target,
                'dibuat_oleh'     => $admin->id,
                'tanggal_publish' => now()->subDays($i * 2)->format('Y-m-d'),
                'is_active'       => true,
            ]);
        }
    }

    /**
     * Tagihan SPP bulanan untuk setiap siswa, dengan sebagian sudah dibayar.
     *
     * Skenario per siswa (3 bulan terakhir):
     *  - Bulan terlama : LUNAS (ada pembayaran disetujui)
     *  - Bulan tengah  : acak — MENUNGGU (ada pembayaran pending) atau LUNAS
     *  - Bulan terbaru : acak — BELUM_DIBAYAR atau MENUNGGU
     *
     * @param  array<int, Siswa>  $siswaList
     */
    private function seedTagihan(array $siswaList, User $admin): void
    {
        $bulanList = [
            ['Mei 2025',  '2025-05-31', 3],
            ['Juni 2025', '2025-06-30', 2],
            ['Juli 2025', '2025-07-31', 1],
        ];
        $nominal = 200_000;

        foreach ($siswaList as $siswa) {
            foreach ($bulanList as [$periode, $jatuhTempo, $bulanLalu]) {
                $isLast   = $bulanLalu === 1;
                $isMid    = $bulanLalu === 2;
                $isOldest = $bulanLalu === 3;

                // Tentukan status tagihan
                if ($isOldest) {
                    $status = Tagihan::STATUS_LUNAS;
                } elseif ($isMid) {
                    $status = fake()->randomElement([Tagihan::STATUS_LUNAS, Tagihan::STATUS_MENUNGGU]);
                } else {
                    $status = fake()->randomElement([Tagihan::STATUS_BELUM, Tagihan::STATUS_MENUNGGU]);
                }

                $tagihan = Tagihan::create([
                    'siswa_id'    => $siswa->id,
                    'judul'       => 'SPP ' . $periode,
                    'jenis'       => 'SPP',
                    'periode'     => $periode,
                    'nominal'     => $nominal,
                    'jatuh_tempo' => $jatuhTempo,
                    'status'      => $status,
                    'keterangan'  => null,
                ]);

                // Buat record pembayaran sesuai status
                if ($status === Tagihan::STATUS_LUNAS) {
                    Pembayaran::create([
                        'tagihan_id'        => $tagihan->id,
                        'nominal'           => $nominal,
                        'metode'            => 'Transfer',
                        'bank'              => fake()->randomElement(['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI']),
                        'nama_pengirim'     => $siswa->nama_lengkap,
                        'tanggal_bayar'     => fake()->dateTimeBetween(
                            now()->subMonths($bulanLalu)->startOfMonth()->format('Y-m-d'),
                            now()->subMonths($bulanLalu)->endOfMonth()->format('Y-m-d')
                        )->format('Y-m-d'),
                        'bukti'             => null,
                        'status'            => Pembayaran::STATUS_DISETUJUI,
                        'catatan'           => 'Pembayaran telah dikonfirmasi.',
                        'diverifikasi_oleh' => $admin->id,
                        'diverifikasi_pada' => now()->subMonths($bulanLalu)->addDays(3),
                    ]);
                } elseif ($status === Tagihan::STATUS_MENUNGGU) {
                    Pembayaran::create([
                        'tagihan_id'    => $tagihan->id,
                        'nominal'       => $nominal,
                        'metode'        => 'Transfer',
                        'bank'          => fake()->randomElement(['BCA', 'BRI', 'BSI', 'Mandiri']),
                        'nama_pengirim' => $siswa->nama_lengkap,
                        'tanggal_bayar' => now()->subDays(fake()->numberBetween(1, 7))->format('Y-m-d'),
                        'bukti'         => null,
                        'status'        => Pembayaran::STATUS_MENUNGGU,
                        'catatan'       => null,
                    ]);
                }
                // STATUS_BELUM: tidak ada record pembayaran
            }
        }
    }
}
