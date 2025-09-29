<?php

namespace App\Services;

class BookCategoryClassifier
{
    /**
     * Daftar judul buku fiksi berdasarkan data yang diberikan
     */
    private static $fiksiTitles = [
        'ABU BAKAR SHIDDIQ', 'AIR MENGALIR SAMPAI JAUH', 'AKIBAT GUNUNG MELETUS', 'AKU ANAK INDONESIA',
        'AMALANKU DARI DASAR IBADAHKU', 'AMALKU dan AMANAT', 'AMBRUKNYA SEBUAH JEMBATAN', 'AMRU BIN ASH SANG PENAKLUK',
        'ANAK BERHATI BAJA', 'ANAK YANG JUJUR', 'Anak yang Tabah', 'ANAK-ANAK PALANG MERAH', 'ANAK-ANAK PANTAI TERATAK',
        'ANTARA BARA DAN YANG MENYALA', 'Anugerah Gunung Merapi', 'ASIH, ASAH, ASUH', 'Awan Kelabu Telah Berlalu',
        'AYAH YANG DIRINDUKAN', 'Ayo Bersikap SPORTIF', 'BADAI MULAI REDA', 'BAKTI SEBUAH KELUARGA',
        'BAKTIKU UNTUKMU', 'BAYANG-BAYANG DI BALIK POHON', 'BENANG MERAH MUAMALAH', 'BERKAH DI BALIK MUSIBAH',
        'BERLIBUR AWAL PERSAHABATAN', 'BERSATU KITA TEGUH', 'BIAS-BIAS KEBAIKAN', 'BINTIK-BINTIK MENTARI KECIL',
        'BUAH TANGAN DARI HUTAN', 'Budi Pekerti kepada Guru', 'BUDIDAYA JAMUR', 'BUDIMAN Anak yang Jujur',
        'BUMI YANG SUBUR DI NEGERI INI', 'BUNGA YANG TAK PERNAH LAYU', 'CINTA KEPADA TUHAN DAN SESAMA',
        'CUT MEUTHIA', 'CUT NYAK DIEN', 'DI ANTARA KECIPAK AIR KOLAM', 'DI DESA KUTEMUKAN KEDAMAIAN',
        'FAJAR RACHMAN', 'HUKUMAN BAGI PENGHIANAT', 'Iman dan Perbuatan Baik', 'Insinyur Cilik',
        'Jalinan Kemanusiaan', 'Jangan Dendam', 'Karyawisata Ke Gunung Galunggung', 'KHALID BIN WALID SI PEDANG ALLAH',
        'KHALIFAH ALI SI PERKASA', 'KRAT-KRAT MENCARI KEADILAN', 'LAUT Mengikat Hati', 'MAAFKAN AKU, SAHABAT',
        'MENGENAL Ubi Jendral', 'MENGGALI HARTA TERPENDAM', 'MENYIBAK KABUT', 'Misteri Gadis Terpasung',
        'MISTERI Kota PERAK', 'MUTIARA DI TELAGA NABI', 'Nabi Ibrahim AS.', 'Nabi Musa dan Tongkat Muk\'jizat',
        'NABI YUSUF AS.', 'NYALA BARA DI DADAKU', 'NYANYIAN SANG GEMBALA', 'OH IBU',
        'PAHLAWAN MUDA', 'PANGERAN HIDAYAT', 'PERANG PAREGREG', 'PERJALANAN PANJANG PENGUNGSIAN',
        'Pertengkaran', 'PESAN PAHLAWAN KECIL', 'PESAN YANG MISTERIUS', 'PUTRA BANTEN',
        'PUTRA HARAPAN BANGSA', 'RAHASIA DI BALIK POHON MURBEI', 'RAJA GEDOBANG', 'RANGKAIAN CERITA TRADISIONAL JAWA TENGAH',
        'SANG JUPITER', 'SEBUAH USAHA YANG TERPADU', 'SEHALUS KAIN SUTRA', 'Selembar Sutera Kenangan',
        'SENYUM Anak Negeri', 'SI KECIL YANG MALANG', 'SULTAN HASANUDDIN', 'SULTAN SURIANSYAH',
        'SULTAN TERNATE', 'SURGA DI BAWAH TELAPAK KAKI IBU', 'TEKAD MENGENTASKAN KEMISKINAN',
        'Tini dan Asih Berwiraswasta', 'UMAR BIN KHATAB', 'API BERKOBAR DI PADANG ILALANG'
    ];

    /**
     * Daftar judul buku non-fiksi berdasarkan data yang diberikan
     */
    private static $nonFiksiTitles = [
        '100 Hadits Qudsi', '99 Asmaul Husna', 'Active Learning 101 Strategi Pembelajaran Aktif', 'Anugerah Gunung Merapi',
        'Asyik Bermain Musik', 'Ayo Belajar Menghitung WARIS', 'Ayo, Bangun untuk Shalat Subuh', 'AYO, BELAJAR SHALAT SUNAT!',
        'Ayo, Mengukur Berat', 'Ayo, Mengukur Jarak', 'Ayo, Mengukur Kelajuan', 'Ayo, Mengukur Suhu', 'Ayo, Mengukur Tinggi',
        'BAHAYA NEOLIBERALISME', 'BERCOCOK TANAM KOPI', 'BERCOCOK TANAM PADI', 'BERCOCOK TANAM POHON BUAH-BUAHAN Yang Baik & Benar',
        'BERKARYA Selagi MUDA', 'Bersahabat dengan Alam', 'BERTANAM TANPA TANAH', 'Borobudur, Pawon dan Mendut',
        'Buaian Ibu Antara Surga & Neraka', 'Cara Membuat Kerupuk', 'CARA MENANAM DAN MENGOLAH KAPOK RANDU',
        'DAKWAH ISLAMIAH', 'DASAR-DASAR BERCOCOK TANAM', 'ENAM HARI YANG MENGGUNCANG DUNIA', 'GANESA TANAH',
        'GANGGUAN PENGGUNAAN ZAT', 'HEAD STRONG Memperkuat Hubungan Otak dan Tubuh untuk Mendapatkan Fisik dan Mental yang Fit',
        'HUTAN MANGROVE', 'ILMU BUDAYA DASAR', 'Isme-Isme yang Mengguncang Dunia', 'KEKUASAAN & KEKERASAN MENURUT JOHAN GALTUNG',
        'Kelembagaan & Organisasi', 'KENALI PARU-PARU ANDA', 'Kesenian Daerah d yang Fitan Lagu-Lagu Daerah',
        'Kisah-Kisah dalam AL-QUR\'AN 1', 'Kisah-Kisah dalam AL-QUR\'AN 2', 'Kisah-Kisah dalam AL-QUR\'AN 3',
        'Kisah-Kisah dalam AL-QUR\'AN 4', 'Kisah-Kisah dalam AL-QUR\'AN 5', 'Kisah-Kisah dalam AL-QUR\'AN 6',
        'KISAH-KISAH JILBAB', 'KISAH-KISAH Terpuji Sahabat Nabi', 'Kita dan Bahasa Inggris', 'KONFLIK BERSEJARAH Sieg Heil! Kisah Pendirian Reich Ketiga',
        'Kumpulan Doa-Doa dalam AL-QUR\'AN', 'Kumpulan HADITS TENTANG AKHLAK TERCELA', 'Kumpulan HADITS TENTANG AKHLAK TERPUJI',
        'Kumpulan Zikir dan Doa dalam HADITS', 'LINTASAN SEJARAH KEBUDAYAAN ISLAM', 'LOGIKA KAIDAH BERPIKIR SECARA TEPAT',
        'Madrasah Sejarah & Perkembangannya', 'MAJAPAHIT', 'MAKASSAR SEBAGAI KOTA MARITIM', 'MANAJER SUKSES',
        'Mari Mengenal Lembar Presentasi (Ms. PowerPoint)', 'MASJID MASJID BERSEJARAH DI INDONESIA', 'MASJID Tinjauan AL-Qur\'an, Al-Sunnah dan Manajemennya',
        'Masuknya ISLAM ke Indonesia', 'Mekanika Terapan', 'MEMBUAT BAK BAMBU SEMEN', 'MEMBUAT GULA SEMUT',
        'Mendulang Intan di Martapura', 'MENGAJAR DENGAN SUKSES', 'Mengenal Adzan', 'Mengenal AQIQAH dan QURBAN',
        'MENGENAL JUAL BELI MENURUT ISLAM', 'Mengenal Pengurusan Jenazah', 'MENGENAL PENYAKIT KULIT DAN CARA PENCEGAHANNYA',
        'Mengenal Puasa', 'Mengenal Tubuh Kita', 'Mengenal Wudhu', 'Mengenal Zakat Mal',
        'Mengupas Ibadah dan Hikmahnya Jilid 1', 'MENIMBA ILMU DARI MUSEUM', 'Menjadi Anak yang Berakhlak Mulia',
        'Menyelidiki Benda Terbang', 'Meraih Kesuksesan', 'Nabi Muhammad SAW sebagai PANGLIMA PERANG',
        'NASEHAT DAN AJARAN BAPAK AKUNTANSI KEPADA PENGUSAHA SUPAYA SUKSES', 'PANCA INDRAKU',
        'PANDANGAN PEREMPUAN TENTANG INDONESIA BARU', 'Panduan Praktis Belajar Komputer', 'PARIWISATA INDONESIA',
        'PELAKU DAN POLITIK EKONOMI INDONESIA', 'PENCEGAHAN DAN PENANGGULANGAN PENYALAHGUNAAN NARKOBA BERBASIS SEKOLAH',
        'PENDIDIKAN ISLAM Tradisi dan Modernisasi Menuju Milenium Baru', 'Pengantar FILSAFAT ILMU',
        'PENGANTAR SEJARAH MUSLIM', 'PENGANTAR UNTUK MEMAHAMI PEMBANGUNAN', 'PENGUJIAN DALAM AUDITING',
        'Peningkatan dan Pengembangan Pendidikan', 'PERANG PAREGREG', 'Perjalanan Sebatang Kayu Jati',
        'Pesona BROMO', 'POLA GERAK DALAM SENAM 1', 'PRAKTIK HUKUM ACARA PERDATA', 'PULAU PERCA BERGOLAK',
        'RADEN WIJAYA KERTARAJASA', 'REMAJA INDUSTRI', 'Riwayat Hidup Perawi HADITS', 'SAINS di Sekitar Kita',
        'SEJARAH KHULAFAUR RASYIDIN', 'SEJARAH PEMBENTUKAN UNDANG-UNDANG DASAR 1945', 'SEJARAH RINGKAS KERAJAAN ISLAM DEMAK',
        'SEKITAR WALISANGA', 'SELALU BERPIKIR POSITIF', 'SENDI-SENDI ILMU HUKUM DAN TATA HUKUM',
        'Seribu KENANGAN', 'STRATEGI MEMBELI BISNIS DAN FRANCHISE TANPA UANG TANPA UTANG', 'Sulam Payet',
        'TELADAN DARI DESA BAPANGAN', 'Terapi Obat dalam Reumatoogi', 'TUANKU IMAM BONJOL', 'TUANKU TAMBUSAI',
        'Udara di Sekitar Kita', 'Undang-Undang Piagam Dan Kisah Negeri Jambi', 'USAMAH MENCARI SYAHID',
        'Warna-Warni Duniaku', 'WASPADA BAHAYA AIDS', 'ALLAH Tidak Berbuat Zalim', 'AMALAN DI BULAN RAMADHAN',
        'Ayat-Ayat Pertama dalam Al-Qur\'an', 'Ayat-Ayat tentang Manusia yang Diberi Petunjuk',
        'AYAT-AYAT tentang Manusia yang Disesatkan', 'AYAT-AYAT TERAKHIR DALAM AL-QUR\'AN',
        'Ayo, Laksanakan Shalat Fardhu', 'BELAJAR SHALAT BERJAMAAH', 'Etika Bertetangga', 'HADITS Tentang Keutamaan NABI SAW',
        'Hidup Berhias Ilmu', 'Jauhi Riba', 'KUMPULAN DOA SEHARI-HARI', 'Makanan yang HALAL & HARAM',
        'Menciptakan Kedamaian', 'Mengenal Haji', 'MENGENAL NAMA-NAMA NEGARA DI DUNIA', 'Mengenal Shalat \'Iedain',
        'Mengikuti Jejak Rasulullas SAW Khulafaur Rasyidin dan Tokoh Agama Islam', 'Mengupas 23 Surat Pilihan',
        'Nama Negara, Wilayah, Tempat, dan Gunung dalam Al-Qur\'an', 'Nama Tempat di AKHIRAT dalam AL-QUR\'AN',
        'Nama-Nama NABI dalam AL-QUR\'AN', 'Perempuan dan Jilbab', 'Rumahku Surgaku', 'Sepintas Mengenal AL-QUR\'AN',
        'Wakaf, Infaq, dan Sadaqah', 'Sehat Itu Nikmat'
    ];

    /**
     * Klasifikasikan buku berdasarkan judul
     */
    public static function classifyBook($title)
    {
        $title = strtoupper(trim($title));
        
        // Cek apakah judul ada dalam daftar fiksi
        if (in_array($title, self::$fiksiTitles)) {
            return 'Buku Bacaan - Fiksi';
        }
        
        // Cek apakah judul ada dalam daftar non-fiksi
        if (in_array($title, self::$nonFiksiTitles)) {
            return 'Buku Bacaan - Non-Fiksi';
        }
        
        // Jika tidak ditemukan, coba cari dengan partial match
        foreach (self::$fiksiTitles as $fiksiTitle) {
            if (strpos($title, $fiksiTitle) !== false || strpos($fiksiTitle, $title) !== false) {
                return 'Buku Bacaan - Fiksi';
            }
        }
        
        foreach (self::$nonFiksiTitles as $nonFiksiTitle) {
            if (strpos($title, $nonFiksiTitle) !== false || strpos($nonFiksiTitle, $title) !== false) {
                return 'Buku Bacaan - Non-Fiksi';
            }
        }
        
        // Default untuk buku yang tidak dikenali
        return 'Buku Bacaan - Non-Fiksi';
    }

    /**
     * Update kategori buku berdasarkan judul
     */
    public static function updateBookCategories()
    {
        $books = \App\Models\Book::whereIn('category', ['Buku Bacaan - Fiksi', 'Buku Bacaan - Non-Fiksi', 'Sastra & Fiksi', 'Fiksi', 'Non-Fiksi', 'Buku Bacaan', 'Novel', 'Cerita'])->get();
        
        $updated = 0;
        foreach ($books as $book) {
            $newCategory = self::classifyBook($book->title);
            if ($book->category !== $newCategory) {
                $book->update(['category' => $newCategory]);
                $updated++;
            }
        }
        
        return $updated;
    }
}

