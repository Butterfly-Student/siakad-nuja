# -*- coding: utf-8 -*-
"""Isi naskah BAB IV & BAB V untuk SIAKAD NUJA."""


def build(g):
    h1 = g["h1"]; h2 = g["h2"]; h3 = g["h3"]; body = g["body"]
    module = g["module"]; figure = g["figure"]; caption_only = g["caption_only"]
    spec_table = g["spec_table"]; blackbox_table = g["blackbox_table"]
    matrix_table = g["matrix_table"]

    # =================================================================
    h1("BAB IV\nHASIL DAN PEMBAHASAN")

    h2("4.1  Hasil")
    body("Bab ini menyajikan hasil penelitian berupa implementasi dan pengujian "
         "Sistem Informasi Akademik (SIAKAD) Yayasan Nurul Jadid Karduluk yang "
         "terintegrasi dengan layanan pesan WhatsApp. Sistem dibangun menggunakan "
         "kerangka kerja Laravel 12 dengan bahasa PHP 8.2, basis data MySQL, serta "
         "antarmuka berbasis Blade yang dipadukan dengan Tailwind CSS dan Alpine.js. "
         "Hasil implementasi dipaparkan dalam bentuk tangkapan layar setiap modul "
         "beserta penjelasannya, kemudian dilanjutkan dengan hasil pengujian "
         "fungsional menggunakan metode black box untuk memastikan seluruh fitur "
         "berjalan sesuai dengan perancangan yang telah dibuat pada bab sebelumnya.")

    # ---- 4.1.1 Lingkungan Implementasi ----
    h3("4.1.1  Lingkungan Implementasi")
    body("Sistem dikembangkan dan diuji coba pada lingkungan dengan spesifikasi "
         "perangkat keras dan perangkat lunak tertentu. Spesifikasi ini menjadi "
         "acuan agar hasil pengujian dapat direproduksi kembali pada lingkungan "
         "yang setara. Rincian spesifikasi perangkat keras yang digunakan selama "
         "proses implementasi disajikan pada Tabel 4.1.")
    caption_only("Tabel 4.1 Spesifikasi Perangkat Keras Implementasi")
    spec_table([
        ("Komponen", "Spesifikasi"),
        ("Prosesor", "Intel Core i5-1135G7 (2.40 GHz, 4 Core / 8 Thread)"),
        ("Memori RAM", "8 GB DDR4"),
        ("Penyimpanan", "512 GB SSD NVMe"),
        ("Kartu Grafis", "Intel Iris Xe Graphics (terintegrasi)"),
        ("Perangkat Uji WhatsApp", "Smartphone Android dengan aplikasi WhatsApp aktif"),
    ])
    body("Selain spesifikasi perangkat keras, perangkat lunak yang digunakan dalam "
         "membangun dan menjalankan sistem turut menentukan kompatibilitas serta "
         "kestabilan aplikasi. Rincian perangkat lunak tersebut disajikan pada "
         "Tabel 4.2.")
    caption_only("Tabel 4.2 Spesifikasi Perangkat Lunak")
    spec_table([
        ("Komponen", "Spesifikasi"),
        ("Sistem Operasi", "Windows 11 Pro 64-bit"),
        ("Bahasa Pemrograman", "PHP 8.2"),
        ("Kerangka Kerja (Framework)", "Laravel 12"),
        ("Basis Data", "MySQL 8.x"),
        ("Web Server Pengembangan", "Artisan Serve (PHP Built-in Server)"),
        ("Antarmuka (Frontend)", "Blade, Tailwind CSS v4, Alpine.js, Vite"),
        ("Gateway WhatsApp", "Go-WA (WhatsApp Gateway berbasis Go)"),
        ("Pustaka Cetak PDF", "barryvdh/laravel-dompdf"),
        ("Antrian (Queue)", "Laravel Queue (driver database)"),
        ("Peramban Uji", "Google Chrome"),
    ])

    # ---- 4.1.2 Implementasi Modul Sistem ----
    h3("4.1.2  Implementasi Modul Sistem")
    body("Berdasarkan perancangan yang telah dilakukan pada bab sebelumnya, seluruh "
         "modul sistem berhasil diimplementasikan menjadi antarmuka yang fungsional. "
         "Sistem memiliki tiga peran pengguna, yaitu Administrator sebagai pengelola "
         "utama data akademik dan keuangan, Guru sebagai penginput nilai dan absensi, "
         "serta Orang Tua/Wali sebagai penerima informasi akademik anak melalui "
         "WhatsApp. Berikut dipaparkan hasil implementasi tiap modul beserta "
         "penjelasan antarmukanya.")

    # a. Autentikasi
    module("a", "Modul Autentikasi (Login)")
    body("Modul autentikasi merupakan pintu masuk sistem yang berfungsi memverifikasi "
         "identitas pengguna sebelum mengakses halaman internal. Pengguna memasukkan "
         "alamat surel dan kata sandi pada formulir yang tersedia. Sistem kemudian "
         "mencocokkan kredensial tersebut dengan data akun pada basis data serta "
         "menentukan peran pengguna guna mengarahkannya ke halaman yang sesuai. "
         "Tampilan halaman login ditunjukkan pada Gambar 4.1.")
    figure("01_login.png", "Gambar 4.1 Halaman Login Sistem")

    # b. Dashboard
    module("b", "Modul Dashboard")
    body("Setelah berhasil masuk, pengguna diarahkan ke halaman dashboard yang "
         "menampilkan ringkasan data akademik sekolah. Pada bagian atas terdapat "
         "kartu statistik ringkas yang memuat jumlah total siswa, guru, kelas, dan "
         "mata pelajaran. Di bawahnya ditampilkan daftar pengumuman terbaru serta "
         "panel pintasan (shortcut) menuju fitur yang paling sering digunakan, "
         "seperti input nilai, absensi, tambah siswa, dan pengumuman. Tampilan "
         "dashboard administrator disajikan pada Gambar 4.2.")
    figure("02_dashboard.png", "Gambar 4.2 Halaman Dashboard Administrator")

    # c. Manajemen Siswa
    module("c", "Modul Manajemen Data Siswa")
    body("Modul manajemen data siswa digunakan administrator untuk mengelola seluruh "
         "data induk siswa. Halaman utama menampilkan daftar siswa dalam bentuk tabel "
         "yang dilengkapi kolom pencarian, sehingga data dapat ditelusuri dengan cepat "
         "berdasarkan nama maupun nomor induk siswa. Setiap baris data menyediakan aksi "
         "untuk melihat detail, mengubah, dan menghapus data. Tampilan daftar siswa "
         "ditunjukkan pada Gambar 4.3.")
    figure("03_siswa_index.png", "Gambar 4.3 Halaman Daftar Data Siswa")
    body("Untuk menambahkan siswa baru, administrator menekan tombol Tambah Siswa "
         "sehingga sistem menampilkan formulir pengisian data. Formulir ini memuat "
         "isian identitas siswa seperti nama, nomor induk siswa (NIS), jenis kelamin, "
         "tanggal lahir, tahun masuk, kelas, serta alamat. Tampilan formulir "
         "penambahan data siswa disajikan pada Gambar 4.4.")
    figure("04_siswa_create.png", "Gambar 4.4 Formulir Tambah Data Siswa")
    body("Setiap data siswa dapat ditinjau secara lengkap melalui halaman detail. "
         "Halaman ini menampilkan biodata siswa, informasi orang tua/wali beserta "
         "nomor kontak, dan rekapitulasi nilai per mata pelajaran lengkap dengan "
         "predikat. Pada halaman ini juga tersedia tombol Kirim Teguran WA yang "
         "memungkinkan administrator mengirimkan pesan teguran kedisiplinan langsung "
         "kepada wali siswa. Tampilan detail siswa ditunjukkan pada Gambar 4.5.")
    figure("05_siswa_detail.png", "Gambar 4.5 Halaman Detail Data Siswa")
    body("Ketika tombol Kirim Teguran WA ditekan, sistem menampilkan jendela dialog "
         "(modal) yang memuat formulir teguran. Melalui jendela ini administrator "
         "dapat memilih jenis pelanggaran serta menuliskan catatan teguran yang akan "
         "dikirimkan ke nomor WhatsApp wali siswa. Fitur ini mendukung fungsi "
         "pemantauan kedisiplinan siswa secara langsung. Tampilan jendela teguran "
         "disajikan pada Gambar 4.6.")
    figure("06_siswa_detail_modal_teguran.png",
           "Gambar 4.6 Jendela Kirim Teguran WhatsApp kepada Wali Siswa")
    body("Data siswa yang telah tersimpan dapat diperbarui melalui halaman ubah data. "
         "Formulir ubah menampilkan seluruh isian yang telah terisi dengan nilai "
         "terkini, sehingga administrator cukup mengubah bagian yang diperlukan. "
         "Tampilan formulir ubah data siswa ditunjukkan pada Gambar 4.7.")
    figure("07_siswa_edit.png", "Gambar 4.7 Formulir Ubah Data Siswa")

    # d. Manajemen Guru
    module("d", "Modul Manajemen Data Guru")
    body("Modul manajemen data guru berfungsi mengelola data pendidik yang mengajar "
         "di sekolah. Halaman daftar guru menampilkan data dalam bentuk tabel yang "
         "memuat nama, nomor induk pegawai (NIP), serta informasi lain, dilengkapi "
         "fitur pencarian dan aksi pengelolaan data. Tampilan daftar guru disajikan "
         "pada Gambar 4.8.")
    figure("08_guru_index.png", "Gambar 4.8 Halaman Daftar Data Guru")
    body("Penambahan data guru dilakukan melalui formulir yang memuat isian identitas "
         "guru seperti nama lengkap, NIP, jenis kelamin, nomor telepon, serta data "
         "kepegawaian lainnya. Tampilan formulir penambahan data guru ditunjukkan "
         "pada Gambar 4.9.")
    figure("09_guru_create.png", "Gambar 4.9 Formulir Tambah Data Guru")
    body("Halaman detail guru menampilkan profil lengkap seorang guru beserta "
         "informasi mata pelajaran dan kelas yang menjadi tanggung jawabnya. Halaman "
         "ini memudahkan administrator memantau penugasan setiap guru. Tampilan "
         "detail guru disajikan pada Gambar 4.10.")
    figure("10_guru_detail.png", "Gambar 4.10 Halaman Detail Data Guru")
    body("Data guru juga dapat diperbarui melalui formulir ubah yang menampilkan data "
         "terkini. Tampilan formulir ubah data guru ditunjukkan pada Gambar 4.11.")
    figure("11_guru_edit.png", "Gambar 4.11 Formulir Ubah Data Guru")

    # e. Kelas
    module("e", "Modul Manajemen Kelas")
    body("Modul manajemen kelas digunakan untuk mengelola rombongan belajar. Halaman "
         "daftar kelas menampilkan seluruh kelas beserta tingkat, jenjang, wali kelas, "
         "dan jumlah siswa pada masing-masing kelas. Tampilan daftar kelas disajikan "
         "pada Gambar 4.12.")
    figure("12_kelas_index.png", "Gambar 4.12 Halaman Daftar Kelas")
    body("Penambahan kelas baru dilakukan melalui formulir yang memuat isian nama "
         "kelas, tingkat, jenjang, tahun ajaran, wali kelas, serta kapasitas kelas. "
         "Tampilan formulir tambah kelas ditunjukkan pada Gambar 4.13.")
    figure("13_kelas_create.png", "Gambar 4.13 Formulir Tambah Kelas")
    body("Halaman detail kelas menampilkan informasi lengkap suatu kelas, meliputi "
         "tingkat, jenjang, tahun ajaran, wali kelas, kapasitas, serta daftar siswa "
         "yang terdaftar di dalamnya. Tampilan detail kelas disajikan pada Gambar 4.14.")
    figure("14_kelas_detail.png", "Gambar 4.14 Halaman Detail Kelas beserta Daftar Siswa")
    body("Data kelas dapat diperbarui melalui formulir ubah kelas sebagaimana "
         "ditunjukkan pada Gambar 4.15.")
    figure("15_kelas_edit.png", "Gambar 4.15 Formulir Ubah Kelas")

    # f. Mata Pelajaran
    module("f", "Modul Manajemen Mata Pelajaran")
    body("Modul mata pelajaran berfungsi mengelola daftar mata pelajaran beserta "
         "Kriteria Ketuntasan Minimal (KKM). Halaman daftar menampilkan kode mapel, "
         "nama mapel, jenjang, dan nilai KKM masing-masing. Tampilan daftar mata "
         "pelajaran ditunjukkan pada Gambar 4.16.")
    figure("16_mapel_index.png", "Gambar 4.16 Halaman Daftar Mata Pelajaran")
    body("Penambahan mata pelajaran dilakukan melalui formulir yang memuat isian kode "
         "mapel, nama mapel, jenjang, KKM, dan deskripsi. Tampilan formulir tambah "
         "mata pelajaran disajikan pada Gambar 4.17.")
    figure("17_mapel_create.png", "Gambar 4.17 Formulir Tambah Mata Pelajaran")
    body("Data mata pelajaran dapat diperbarui melalui formulir ubah sebagaimana "
         "ditunjukkan pada Gambar 4.18.")
    figure("18_mapel_edit.png", "Gambar 4.18 Formulir Ubah Mata Pelajaran")

    # g. Jadwal
    module("g", "Modul Jadwal Pelajaran")
    body("Modul jadwal pelajaran digunakan untuk menyusun jadwal mengajar per kelas. "
         "Halaman daftar jadwal menampilkan hari, jam ke-, waktu, mata pelajaran, "
         "kelas, guru pengampu, dan ruangan. Halaman ini dilengkapi penyaring "
         "berdasarkan kelas dan hari sehingga jadwal dapat ditinjau lebih ringkas. "
         "Tampilan daftar jadwal disajikan pada Gambar 4.19.")
    figure("19_jadwal_index.png", "Gambar 4.19 Halaman Daftar Jadwal Pelajaran")
    body("Penambahan jadwal dilakukan melalui formulir yang memuat isian kelas, mata "
         "pelajaran, guru, hari, jam ke-, ruangan, jam mulai, jam selesai, serta tahun "
         "ajaran. Tampilan formulir tambah jadwal ditunjukkan pada Gambar 4.20.")
    figure("20_jadwal_create.png", "Gambar 4.20 Formulir Tambah Jadwal Pelajaran")

    # h. Nilai
    module("h", "Modul Penilaian")
    body("Modul penilaian merupakan salah satu modul inti yang mengelola nilai siswa "
         "per mata pelajaran. Halaman daftar nilai menampilkan nama siswa, mata "
         "pelajaran, kelas, semester, nilai harian, nilai UTS, nilai UAS, nilai akhir, "
         "dan predikat. Nilai akhir dihitung secara otomatis oleh sistem menggunakan "
         "pembobotan nilai harian 30%, UTS 30%, dan UAS 40%, kemudian dikonversi "
         "menjadi predikat serta status ketuntasan berdasarkan KKM mata pelajaran. "
         "Dengan demikian, guru cukup memasukkan komponen nilai tanpa perlu menghitung "
         "nilai akhir secara manual. Tampilan daftar nilai disajikan pada Gambar 4.21.")
    figure("21_nilai_index.png", "Gambar 4.21 Halaman Data Nilai Siswa")

    # i. Absensi
    module("i", "Modul Absensi")
    body("Modul absensi digunakan untuk merekam kehadiran siswa pada setiap jadwal "
         "pelajaran. Halaman daftar absensi menampilkan tanggal, nama siswa, mata "
         "pelajaran, kelas, status kehadiran (hadir, sakit, izin, atau alpa), serta "
         "keterangan. Ketika siswa tercatat tidak hadir, sistem dapat mengirimkan "
         "notifikasi kehadiran secara otomatis kepada wali siswa melalui WhatsApp. "
         "Tampilan data absensi disajikan pada Gambar 4.22.")
    figure("22_absensi_index.png", "Gambar 4.22 Halaman Data Absensi Siswa")

    # j. Tagihan
    module("j", "Modul Tagihan dan Pembayaran")
    body("Modul tagihan dan pembayaran mengelola administrasi keuangan sekolah, "
         "khususnya tagihan Sumbangan Pembinaan Pendidikan (SPP). Halaman utama "
         "menampilkan kartu ringkasan yang memuat total tagihan, jumlah tagihan lunas, "
         "tagihan yang menunggu verifikasi, dan jumlah tunggakan. Di bawahnya terdapat "
         "tabel tagihan lengkap dengan nama siswa, kelas, judul tagihan, periode, "
         "nominal, jatuh tempo, dan status pembayaran, disertai penyaring berdasarkan "
         "kelas dan status. Tampilan daftar tagihan ditunjukkan pada Gambar 4.23.")
    figure("23_tagihan_index.png", "Gambar 4.23 Halaman Tagihan dan Pembayaran")
    body("Pembuatan tagihan dapat dilakukan secara perorangan (per siswa) maupun "
         "massal per kelas. Formulir pembuatan tagihan memuat isian siswa, judul "
         "tagihan, jenis tagihan, periode, nominal, jatuh tempo, dan keterangan. "
         "Ketika tagihan baru dibuat, sistem dapat mengirimkan notifikasi kepada wali "
         "siswa melalui WhatsApp. Tampilan formulir pembuatan tagihan disajikan pada "
         "Gambar 4.24.")
    figure("24_tagihan_create.png", "Gambar 4.24 Formulir Pembuatan Tagihan")
    body("Setiap tagihan dapat ditinjau lebih rinci melalui halaman detail. Halaman "
         "ini menampilkan informasi siswa, rincian tagihan, dan riwayat pembayaran "
         "beserta status verifikasinya. Administrator dapat memverifikasi pembayaran "
         "yang diunggah wali siswa, dan setelah disetujui sistem mengirimkan konfirmasi "
         "pembayaran melalui WhatsApp. Tampilan detail tagihan ditunjukkan pada "
         "Gambar 4.25.")
    figure("25_tagihan_detail.png", "Gambar 4.25 Halaman Detail Tagihan dan Riwayat Pembayaran")
    body("Data tagihan dapat diperbarui melalui formulir ubah tagihan sebagaimana "
         "ditunjukkan pada Gambar 4.26.")
    figure("26_tagihan_edit.png", "Gambar 4.26 Formulir Ubah Tagihan")

    # k. Laporan
    module("k", "Modul Laporan Akademik")
    body("Modul laporan akademik menyediakan fasilitas pembuatan rekapitulasi data "
         "akademik dalam bentuk berkas yang dapat diunduh. Halaman ini memuat dua "
         "jenis laporan, yaitu Rekap Kehadiran Kelas dan Rekap Nilai Kelas. Pengguna "
         "memilih kelas serta parameter lain, lalu dapat melihat pratinjau maupun "
         "mengunduh laporan dalam format PDF dan CSV. Tampilan halaman laporan "
         "akademik disajikan pada Gambar 4.27.")
    figure("27_laporan_index.png", "Gambar 4.27 Halaman Laporan Akademik")
    body("Rekap Kehadiran Kelas menghasilkan laporan jumlah kehadiran siswa per kelas "
         "dalam satu bulan tertentu. Administrator memilih kelas dan bulan, kemudian "
         "sistem menyusun rekap kehadiran yang dapat dicetak dalam format PDF maupun "
         "diunduh dalam format CSV. Tampilan bagian rekap kehadiran ditunjukkan pada "
         "Gambar 4.28.")
    figure("28_laporan_kehadiran.png", "Gambar 4.28 Fitur Rekap Kehadiran Kelas")
    body("Rekap Nilai Kelas menghasilkan laporan nilai akhir siswa untuk mata "
         "pelajaran tertentu pada suatu kelas. Laporan ini membantu wali kelas dan "
         "guru dalam mengevaluasi capaian belajar siswa secara menyeluruh. Tampilan "
         "bagian rekap nilai disajikan pada Gambar 4.29.")
    figure("29_laporan_nilai.png", "Gambar 4.29 Fitur Rekap Nilai Kelas")

    # l. Pengumuman
    module("l", "Modul Pengumuman")
    body("Modul pengumuman digunakan untuk menyampaikan informasi resmi kepada warga "
         "sekolah. Halaman daftar pengumuman menampilkan judul, target penerima, "
         "tanggal publikasi, pembuat, dan status pengumuman. Tampilan daftar "
         "pengumuman ditunjukkan pada Gambar 4.30.")
    figure("30_pengumuman_index.png", "Gambar 4.30 Halaman Daftar Pengumuman")
    body("Pembuatan pengumuman dilakukan melalui formulir yang memuat isian judul, "
         "konten, target peran, target kelas, dan tanggal publikasi. Terdapat pula "
         "opsi untuk menyiarkan pengumuman secara otomatis melalui WhatsApp kepada "
         "kelas atau seluruh sekolah yang menjadi sasaran. Tampilan formulir "
         "pembuatan pengumuman disajikan pada Gambar 4.31.")
    figure("31_pengumuman_create.png", "Gambar 4.31 Formulir Pembuatan Pengumuman")
    body("Setiap pengumuman dapat ditinjau melalui halaman detail yang menampilkan "
         "judul, target, status, tanggal publikasi, pembuat, dan isi lengkap "
         "pengumuman. Tampilan detail pengumuman ditunjukkan pada Gambar 4.32.")
    figure("32_pengumuman_detail.png", "Gambar 4.32 Halaman Detail Pengumuman")
    body("Pengumuman yang telah dibuat dapat diperbarui melalui formulir ubah "
         "sebagaimana ditunjukkan pada Gambar 4.33.")
    figure("33_pengumuman_edit.png", "Gambar 4.33 Formulir Ubah Pengumuman")

    # m. WhatsApp Gateway
    module("m", "Modul WhatsApp Gateway")
    body("Modul WhatsApp Gateway merupakan modul yang menjadikan sistem ini berbeda "
         "dari sistem informasi akademik pada umumnya. Modul ini mengelola integrasi "
         "antara sistem dengan layanan pesan WhatsApp melalui gateway Go-WA. Halaman "
         "utama menampilkan ringkasan total notifikasi, jumlah notifikasi gagal, dan "
         "jumlah sesi chatbot, serta status koneksi perangkat WhatsApp yang dilengkapi "
         "tombol untuk menghubungkan ulang maupun keluar. Tampilan halaman WhatsApp "
         "Gateway disajikan pada Gambar 4.34.")
    figure("34_whatsapp_index.png", "Gambar 4.34 Halaman WhatsApp Gateway dan Status Koneksi")
    body("Sistem menyediakan halaman pengaturan template pesan agar administrator "
         "dapat menyesuaikan isi setiap notifikasi otomatis. Template mendukung "
         "penggunaan variabel (placeholder) seperti {nama_wali}, {nama_siswa}, "
         "{kelas}, {status}, dan {tanggal} yang akan digantikan dengan data "
         "sesungguhnya saat pesan dikirim. Terdapat template untuk notifikasi absensi, "
         "nilai baru, tagihan baru, pengumuman, dan konfirmasi pembayaran. Tampilan "
         "pengaturan template pesan ditunjukkan pada Gambar 4.35.")
    figure("35_whatsapp_templates.png", "Gambar 4.35 Halaman Pengaturan Template Pesan WhatsApp")
    body("Seluruh pesan notifikasi yang dikirim sistem terekam pada halaman log "
         "notifikasi. Halaman ini menampilkan nomor tujuan, siswa terkait, jenis "
         "notifikasi, isi pesan, status pengiriman (terkirim atau gagal), dan waktu "
         "pengiriman. Notifikasi yang gagal dapat dikirim ulang melalui tombol Kirim "
         "Ulang, sehingga proses pengiriman lebih andal. Tampilan log notifikasi "
         "disajikan pada Gambar 4.36.")
    figure("36_whatsapp_log_notifikasi.png", "Gambar 4.36 Halaman Log Notifikasi WhatsApp")
    body("Selain mengirim notifikasi, sistem juga menyediakan chatbot yang dapat "
         "menjawab pertanyaan wali siswa secara otomatis. Halaman log chatbot mencatat "
         "seluruh percakapan, meliputi nomor pengirim, siswa terkait, pesan masuk, "
         "balasan bot, dan intent yang dikenali seperti SHOW_MENU, INFO_NILAI, "
         "INFO_KEHADIRAN, INFO_TAGIHAN, INFO_AGENDA, dan CS_CONTACT. Nomor yang tidak "
         "terdaftar sebagai wali akan menerima balasan penolakan sehingga privasi data "
         "tetap terjaga. Tampilan log chatbot ditunjukkan pada Gambar 4.37.")
    figure("37_whatsapp_log_chatbot.png", "Gambar 4.37 Halaman Log Percakapan Chatbot WhatsApp")

    # n. Orang Tua
    module("n", "Modul Data Orang Tua")
    body("Modul data orang tua mengelola data wali siswa yang menjadi penerima "
         "notifikasi WhatsApp. Halaman daftar menampilkan nama orang tua, hubungan "
         "dengan siswa, nama siswa, nomor telepon, dan penanda kontak utama. Nomor "
         "telepon inilah yang digunakan sistem sebagai tujuan pengiriman seluruh "
         "notifikasi otomatis. Tampilan data orang tua disajikan pada Gambar 4.38.")
    figure("38_orang_tua_index.png", "Gambar 4.38 Halaman Data Orang Tua/Wali Siswa")

    # o. Manajemen Akun
    module("o", "Modul Manajemen Akun")
    body("Modul manajemen akun digunakan administrator untuk mengelola akun pengguna "
         "sistem, yaitu akun admin dan guru. Halaman daftar menampilkan nama, surel, "
         "peran, nomor telepon, dan status akun, dilengkapi fitur pencarian dan "
         "penyaring peran. Tampilan halaman manajemen akun ditunjukkan pada Gambar 4.39.")
    figure("39_users_index.png", "Gambar 4.39 Halaman Manajemen Akun")
    body("Penambahan akun baru dilakukan melalui formulir yang memuat isian nama "
         "lengkap, surel, peran, kata sandi, nomor telepon, NIP, jabatan, dan status "
         "aktif akun. Melalui formulir ini administrator dapat menentukan hak akses "
         "pengguna sesuai perannya. Tampilan formulir penambahan akun disajikan pada "
         "Gambar 4.40.")
    figure("40_users_create.png", "Gambar 4.40 Formulir Tambah Akun")

    # ---- 4.1.3 Pengujian Black Box ----
    _blackbox(g)

    # ---- 4.2 Pembahasan ----
    _pembahasan(g)

    # ---- BAB V ----
    _bab5(g)


def _blackbox(g):
    h3 = g["h3"]; body = g["body"]; caption_only = g["caption_only"]
    blackbox_table = g["blackbox_table"]

    h3("4.1.3  Hasil Pengujian Black Box")
    body("Pengujian sistem dilakukan menggunakan metode black box, yaitu pengujian "
         "yang berfokus pada kesesuaian keluaran sistem terhadap masukan yang "
         "diberikan tanpa memperhatikan struktur kode program di baliknya. Pengujian "
         "ini bertujuan memastikan setiap fungsi berjalan sesuai dengan yang "
         "diharapkan. Hasil pengujian dikelompokkan berdasarkan modul dan disajikan "
         "pada tabel-tabel berikut.")

    caption_only("Tabel 4.3 Hasil Pengujian Black Box - Modul Autentikasi")
    blackbox_table([
        ["1", "Login dengan kredensial valid",
         "Surel dan kata sandi benar",
         "Pengguna berhasil masuk dan diarahkan ke dashboard",
         "Pengguna masuk dan diarahkan ke dashboard sesuai peran", "Berhasil"],
        ["2", "Login dengan kredensial salah",
         "Surel atau kata sandi salah",
         "Sistem menolak dan menampilkan pesan kesalahan",
         "Sistem menampilkan pesan kredensial tidak sesuai", "Berhasil"],
        ["3", "Akses halaman tanpa login",
         "Membuka URL halaman internal",
         "Sistem mengalihkan ke halaman login",
         "Pengguna dialihkan ke halaman login", "Berhasil"],
    ])

    caption_only("Tabel 4.4 Hasil Pengujian Black Box - Modul Data Master")
    blackbox_table([
        ["1", "Tambah data siswa",
         "Mengisi formulir siswa dengan lengkap",
         "Data siswa tersimpan dan tampil pada daftar",
         "Data siswa tersimpan dan tampil pada daftar", "Berhasil"],
        ["2", "Ubah data siswa",
         "Mengubah salah satu isian lalu menyimpan",
         "Data siswa diperbarui sesuai perubahan",
         "Data siswa berhasil diperbarui", "Berhasil"],
        ["3", "Hapus data siswa",
         "Menekan tombol hapus pada suatu baris",
         "Data siswa terhapus dari daftar",
         "Data siswa terhapus dari daftar", "Berhasil"],
        ["4", "Tambah data guru, kelas, dan mapel",
         "Mengisi formulir masing-masing modul",
         "Data tersimpan dan tampil pada daftar",
         "Seluruh data tersimpan dan tampil", "Berhasil"],
        ["5", "Pencarian data",
         "Mengetik kata kunci pada kolom pencarian",
         "Daftar tersaring sesuai kata kunci",
         "Daftar tersaring sesuai kata kunci", "Berhasil"],
    ])

    caption_only("Tabel 4.5 Hasil Pengujian Black Box - Modul Nilai dan Absensi")
    blackbox_table([
        ["1", "Input nilai siswa",
         "Nilai harian, UTS, dan UAS",
         "Nilai akhir dan predikat terhitung otomatis",
         "Nilai akhir (30-30-40) dan predikat terhitung otomatis", "Berhasil"],
        ["2", "Penentuan status ketuntasan",
         "Nilai akhir dibanding KKM mapel",
         "Status tuntas/remedial ditentukan otomatis",
         "Status ketuntasan ditentukan sesuai KKM", "Berhasil"],
        ["3", "Input absensi siswa",
         "Memilih status kehadiran per siswa",
         "Data absensi tersimpan sesuai status",
         "Data absensi tersimpan sesuai status", "Berhasil"],
    ])

    caption_only("Tabel 4.6 Hasil Pengujian Black Box - Modul Tagihan dan Pembayaran")
    blackbox_table([
        ["1", "Buat tagihan per siswa",
         "Mengisi formulir tagihan per siswa",
         "Tagihan tersimpan dengan status belum dibayar",
         "Tagihan tersimpan berstatus belum dibayar", "Berhasil"],
        ["2", "Buat tagihan massal per kelas",
         "Memilih kelas lalu membuat tagihan",
         "Tagihan dibuat untuk seluruh siswa di kelas",
         "Tagihan dibuat untuk seluruh siswa kelas terpilih", "Berhasil"],
        ["3", "Verifikasi pembayaran",
         "Menyetujui bukti pembayaran wali",
         "Status tagihan berubah menjadi lunas",
         "Status berubah lunas dan riwayat tercatat", "Berhasil"],
    ])

    caption_only("Tabel 4.7 Hasil Pengujian Black Box - Modul Laporan")
    blackbox_table([
        ["1", "Cetak rekap kehadiran (PDF)",
         "Memilih kelas dan bulan",
         "Berkas PDF rekap kehadiran terunduh",
         "Berkas PDF rekap kehadiran berhasil terunduh", "Berhasil"],
        ["2", "Unduh rekap nilai (CSV)",
         "Memilih kelas dan mata pelajaran",
         "Berkas CSV rekap nilai terunduh",
         "Berkas CSV rekap nilai berhasil terunduh", "Berhasil"],
    ])

    caption_only("Tabel 4.8 Hasil Pengujian Black Box - Modul WhatsApp Gateway")
    blackbox_table([
        ["1", "Kirim notifikasi otomatis",
         "Peristiwa absensi/nilai/tagihan baru",
         "Notifikasi terkirim ke WhatsApp wali",
         "Notifikasi terkirim dan tercatat pada log", "Berhasil"],
        ["2", "Kirim ulang notifikasi gagal",
         "Menekan tombol Kirim Ulang",
         "Sistem mengirim ulang pesan yang gagal",
         "Pesan dikirim ulang dan status diperbarui", "Berhasil"],
        ["3", "Balasan chatbot untuk wali terdaftar",
         "Wali mengirim menu/angka pilihan",
         "Bot membalas info sesuai intent",
         "Bot membalas info nilai/kehadiran/tagihan/agenda", "Berhasil"],
        ["4", "Balasan chatbot untuk nomor tak terdaftar",
         "Nomor bukan wali mengirim pesan",
         "Bot menolak dengan pesan pemberitahuan",
         "Bot membalas nomor tidak terdaftar", "Berhasil"],
        ["5", "Siar pengumuman via WhatsApp",
         "Membuat pengumuman dengan opsi siar",
         "Pengumuman terkirim ke kelas sasaran",
         "Pengumuman tersiar ke nomor wali kelas sasaran", "Berhasil"],
    ])


def _pembahasan(g):
    h2 = g["h2"]; h3 = g["h3"]; body = g["body"]
    caption_only = g["caption_only"]; matrix_table = g["matrix_table"]

    h2("4.2  Pembahasan")

    h3("4.2.1  Analisis Hasil Pengujian")
    body("Berdasarkan hasil pengujian black box yang disajikan pada Tabel 4.3 hingga "
         "Tabel 4.8, seluruh skenario uji memperoleh status Berhasil. Hal ini "
         "menunjukkan bahwa setiap fungsi pada sistem, mulai dari autentikasi, "
         "pengelolaan data master, penilaian, absensi, tagihan, laporan, hingga "
         "integrasi WhatsApp, telah berjalan sesuai dengan perancangan. Fitur "
         "penghitungan nilai akhir secara otomatis dengan pembobotan 30% nilai harian, "
         "30% UTS, dan 40% UAS terbukti mengurangi kemungkinan kesalahan hitung "
         "manual, sekaligus mempercepat proses penilaian yang dilakukan guru.")
    body("Fitur laporan akademik dalam format PDF dan CSV membantu pihak sekolah "
         "menyusun dokumen kehadiran dan nilai secara cepat dan rapi. Sementara itu, "
         "fitur tagihan yang dilengkapi ringkasan status dan verifikasi pembayaran "
         "memudahkan bagian keuangan dalam memantau kondisi pembayaran SPP setiap "
         "siswa. Dengan demikian, sistem tidak hanya menggantikan pencatatan manual, "
         "tetapi juga meningkatkan efisiensi kerja pada berbagai lini administrasi "
         "sekolah.")

    h3("4.2.2  Peran Integrasi WhatsApp dalam Keterlibatan Orang Tua")
    body("Keunggulan utama sistem ini terletak pada integrasi dengan layanan pesan "
         "WhatsApp. Berbeda dengan pendekatan konvensional yang mengharuskan orang tua "
         "membuka aplikasi atau portal khusus untuk memperoleh informasi akademik, "
         "sistem ini memanfaatkan WhatsApp sebagai kanal yang telah familier dan "
         "digunakan sehari-hari oleh wali siswa. Notifikasi otomatis dikirim ketika "
         "terjadi peristiwa penting seperti ketidakhadiran siswa, penginputan nilai "
         "baru, penerbitan tagihan, dan konfirmasi pembayaran, sehingga orang tua "
         "memperoleh informasi secara langsung tanpa perlu menunggu.")
    body("Selain notifikasi satu arah, sistem juga menyediakan chatbot dua arah yang "
         "memungkinkan wali siswa memperoleh informasi secara mandiri melalui balasan "
         "otomatis. Perbandingan pendekatan integrasi WhatsApp pada sistem ini "
         "terhadap pendekatan konvensional disajikan pada Tabel 4.9.")
    caption_only("Tabel 4.9 Perbandingan Pendekatan Penyampaian Informasi kepada Orang Tua")
    matrix_table(
        ["Aspek", "Pendekatan Konvensional", "SIAKAD NUJA (Integrasi WhatsApp)"],
        [
            ["Kanal informasi", "Buku penghubung / portal khusus",
             "WhatsApp yang sudah biasa digunakan"],
            ["Kecepatan informasi", "Tertunda, bergantung pertemuan",
             "Real-time saat peristiwa terjadi"],
            ["Notifikasi absensi", "Manual atau tidak ada",
             "Otomatis saat siswa tidak hadir"],
            ["Informasi nilai", "Menunggu pembagian rapor",
             "Notifikasi saat nilai diinput"],
            ["Informasi tagihan", "Surat edaran fisik",
             "Notifikasi dan chatbot info tagihan"],
            ["Interaksi orang tua", "Satu arah",
             "Dua arah melalui chatbot"],
            ["Rekam jejak pesan", "Sulit ditelusuri",
             "Tercatat pada log notifikasi & chatbot"],
        ])
    body("Berdasarkan perbandingan tersebut, integrasi WhatsApp pada SIAKAD NUJA "
         "terbukti mampu meningkatkan keterlibatan orang tua dalam memantau "
         "perkembangan akademik dan administrasi anak. Adanya log notifikasi dan log "
         "chatbot juga memberikan rekam jejak yang jelas atas setiap pesan yang "
         "dikirim maupun diterima, sehingga sekolah dapat memastikan informasi benar-"
         "benar tersampaikan kepada wali siswa.")


def _bab5(g):
    h1 = g["h1"]; h2 = g["h2"]; body = g["body"]

    doc = g["doc"]
    doc.add_page_break()
    h1("BAB V\nKESIMPULAN DAN SARAN")

    h2("5.1  Kesimpulan")
    body("Berdasarkan hasil penelitian, implementasi, dan pengujian yang telah "
         "dilakukan terhadap Sistem Informasi Akademik Yayasan Nurul Jadid Karduluk "
         "yang terintegrasi dengan WhatsApp, dapat ditarik beberapa kesimpulan sebagai "
         "berikut:")
    _numbered(g, [
        "Sistem Informasi Akademik SIAKAD NUJA berhasil dibangun menggunakan kerangka "
        "kerja Laravel 12 dan basis data MySQL, serta mampu mengelola data akademik "
        "sekolah secara terpusat, meliputi data siswa, guru, kelas, mata pelajaran, "
        "jadwal, nilai, absensi, tagihan, laporan, pengumuman, dan akun pengguna.",
        "Sistem mampu menghitung nilai akhir siswa secara otomatis dengan pembobotan "
        "nilai harian 30%, UTS 30%, dan UAS 40%, serta menentukan predikat dan status "
        "ketuntasan berdasarkan KKM, sehingga mempercepat proses penilaian dan "
        "mengurangi kesalahan perhitungan manual.",
        "Integrasi dengan gateway WhatsApp (Go-WA) memungkinkan sistem mengirimkan "
        "notifikasi otomatis kepada wali siswa terkait absensi, nilai, tagihan, "
        "konfirmasi pembayaran, dan pengumuman, serta menyediakan chatbot dua arah "
        "yang dapat menjawab pertanyaan wali siswa secara mandiri.",
        "Hasil pengujian black box terhadap seluruh modul menunjukkan bahwa setiap "
        "fungsi berjalan sesuai dengan perancangan, sehingga sistem dinyatakan layak "
        "digunakan untuk mendukung kegiatan administrasi dan penyampaian informasi "
        "akademik di Yayasan Nurul Jadid Karduluk.",
    ])

    h2("5.2  Saran")
    body("Sistem yang telah dibangun masih memiliki ruang untuk dikembangkan lebih "
         "lanjut. Beberapa saran yang dapat menjadi acuan pengembangan berikutnya "
         "adalah sebagai berikut:")
    _numbered(g, [
        "Menambahkan aplikasi berbasis perangkat bergerak (mobile) agar orang tua dan "
        "guru dapat mengakses sistem dengan lebih mudah selain melalui WhatsApp.",
        "Melengkapi sistem dengan integrasi pembayaran daring (payment gateway) "
        "sehingga wali siswa dapat membayar tagihan SPP secara langsung tanpa "
        "verifikasi manual.",
        "Mengembangkan kemampuan chatbot dengan pemrosesan bahasa alami (natural "
        "language processing) agar dapat memahami pertanyaan wali siswa yang lebih "
        "beragam, tidak terbatas pada menu angka.",
        "Menambahkan fitur analitik dan visualisasi data akademik pada dashboard, "
        "seperti grafik perkembangan nilai dan tingkat kehadiran, untuk mendukung "
        "pengambilan keputusan pihak sekolah.",
        "Meningkatkan aspek keamanan sistem, antara lain melalui autentikasi dua "
        "faktor dan pencadangan basis data secara berkala.",
    ])


def _numbered(g, items):
    doc = g["doc"]
    from docx.enum.text import WD_ALIGN_PARAGRAPH
    from docx.shared import Emu
    for i, it in enumerate(items, 1):
        p = doc.add_paragraph(style="Normal")
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
        p.paragraph_format.left_indent = Emu(457200)
        p.paragraph_format.first_line_indent = Emu(-285750)
        p.add_run("%d.  %s" % (i, it))
