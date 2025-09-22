<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookImportController extends Controller
{
    public function showImportForm()
    {
        return view('books.import-excel');
    }

    public function importExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
            'data_type' => 'required|in:koleksi,bacaan,paket',
            'skip_duplicates' => 'boolean',
            'update_existing' => 'boolean',
            'add_quantity' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('excel_file');
            $dataType = $request->input('data_type');
            $skipDuplicates = $request->boolean('skip_duplicates', true);
            $updateExisting = $request->boolean('update_existing', false);
            $addQuantity = $request->boolean('add_quantity', false);

            // Read CSV file
            $data = [];
            if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }

            // Remove empty rows
            $data = array_filter($data, function($row) {
                return !empty(array_filter($row));
            });

            if (empty($data)) {
                return back()->with('error', 'File CSV kosong atau tidak memiliki data.');
            }

            $importedCount = 0;
            $skippedCount = 0;
            $updatedCount = 0;
            $errors = [];

            DB::beginTransaction();

            try {
                foreach ($data as $index => $row) {
                    // Skip header rows
                    if ($index < 2) continue;

                    try {
                        $bookData = $this->parseRowData($row, $dataType);
                        
                        if (empty($bookData['title'])) {
                            continue; // Skip empty rows
                        }

                        // Check for existing book
                        $existingBook = $this->findExistingBook($bookData);

                        if ($existingBook) {
                            if ($skipDuplicates && !$updateExisting) {
                                $skippedCount++;
                                continue;
                            }

                            if ($updateExisting) {
                                $this->updateExistingBook($existingBook, $bookData, $addQuantity);
                                $updatedCount++;
                            }
                        } else {
                            $this->createNewBook($bookData);
                            $importedCount++;
                        }

                    } catch (\Exception $e) {
                        $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
                    }
                }

                DB::commit();

                $message = "Import berhasil! ";
                $message .= "Diimpor: {$importedCount} buku, ";
                $message .= "Diupdate: {$updatedCount} buku, ";
                $message .= "Dilewati: {$skippedCount} buku.";

                if (!empty($errors)) {
                    $message .= " Terjadi " . count($errors) . " error.";
                }

                return back()->with('success', $message)->with('errors', $errors);

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    private function parseRowData($row, $dataType)
    {
        switch ($dataType) {
            case 'koleksi':
                return $this->parseKoleksiData($row);
            case 'bacaan':
                return $this->parseBacaanData($row);
            case 'paket':
                return $this->parsePaketData($row);
            default:
                throw new \Exception('Jenis data tidak valid');
        }
    }

    private function parseKoleksiData($row)
    {
        // Format: No, Jenis Koleksi, Judul, Pengarang, Penerbit, Jumlah
        if (count($row) < 6) {
            throw new \Exception('Format data tidak lengkap');
        }

        $title = trim($row[2] ?? '');
        $jenisKoleksi = trim($row[1] ?? '');
        $category = $this->determineKoleksiCategory($jenisKoleksi, $title);

        return [
            'title' => $title,
            'author' => trim($row[3] ?? ''),
            'publisher' => trim($row[4] ?? ''),
            'category' => $category,
            'quantity' => (int)($row[5] ?? 0),
            'available_quantity' => (int)($row[5] ?? 0),
            'isbn' => $this->generateISBN(),
            'kelas' => $this->extractKelasFromTitle($title),
        ];
    }

    private function parseBacaanData($row)
    {
        // Format: No, Judul, Pengarang, Penerbit, Jumlah
        if (count($row) < 5) {
            throw new \Exception('Format data tidak lengkap');
        }

        $title = trim($row[1] ?? '');
        $category = $this->determineBacaanCategory($title);

        return [
            'title' => $title,
            'author' => trim($row[2] ?? ''),
            'publisher' => trim($row[3] ?? ''),
            'category' => $category,
            'quantity' => (int)($row[4] ?? 0),
            'available_quantity' => (int)($row[4] ?? 0),
            'isbn' => $this->generateISBN(),
            'kelas' => $this->extractKelasFromTitle($title),
        ];
    }

    private function parsePaketData($row)
    {
        // Format: Nama Buku, Jumlah
        if (count($row) < 2) {
            throw new \Exception('Format data tidak lengkap');
        }

        $title = trim($row[0] ?? '');
        $category = $this->determinePaketCategory($title);

        return [
            'title' => $title,
            'author' => 'Tim Penulis',
            'publisher' => 'Penerbit Resmi',
            'category' => $category,
            'quantity' => (int)($row[1] ?? 0),
            'available_quantity' => (int)($row[1] ?? 0),
            'isbn' => $this->generateISBN(),
            'kelas' => $this->extractKelasFromTitle($title),
        ];
    }

    private function findExistingBook($bookData)
    {
        // Try to find by ISBN first, then by title
        if (!empty($bookData['isbn'])) {
            $book = Book::where('isbn', $bookData['isbn'])->first();
            if ($book) return $book;
        }

        return Book::where('title', $bookData['title'])
                  ->where('author', $bookData['author'])
                  ->first();
    }

    private function createNewBook($bookData)
    {
        return Book::create([
            'title' => $bookData['title'],
            'author' => $bookData['author'],
            'publisher' => $bookData['publisher'],
            'category' => $bookData['category'],
            'quantity' => $bookData['quantity'],
            'available_quantity' => $bookData['available_quantity'],
            'isbn' => $bookData['isbn'],
            'kelas' => $bookData['kelas'],
            'status' => 'active',
        ]);
    }

    private function updateExistingBook($book, $bookData, $addQuantity)
    {
        $book->update([
            'publisher' => $bookData['publisher'],
            'category' => $bookData['category'],
            'quantity' => $addQuantity ? $book->quantity + $bookData['quantity'] : $bookData['quantity'],
            'available_quantity' => $addQuantity ? $book->available_quantity + $bookData['available_quantity'] : $bookData['available_quantity'],
            'kelas' => $bookData['kelas'],
        ]);
    }

    private function generateISBN()
    {
        // Generate a simple ISBN-like number
        return '978-' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(100000, 999999) . '-' . rand(0, 9);
    }

    private function extractKelasFromTitle($title)
    {
        // Extract class information from title
        $patterns = [
            '/kelas\s+([ivx]+|\d+)/i',
            '/class\s+([ivx]+|\d+)/i',
            '/([ivx]+|\d+)\s+kelas/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $title, $matches)) {
                return strtoupper($matches[1]);
            }
        }

        return null;
    }

    private function determineBacaanCategory($title, $sheetType = null)
    {
        // Jika ada informasi sheet type, gunakan itu
        if ($sheetType) {
            return $sheetType === 'fiksi' ? 'Fiksi' : 'Non-Fiksi';
        }
        
        $title = strtolower($title);
        
        // Keywords untuk Fiksi berdasarkan data yang ada
        $fiksiKeywords = [
            'abu bakar', 'air mengalir', 'akibat gunung', 'aku anak indonesia', 'amalanku', 'amalku',
            'ambruknya', 'amru bin ash', 'anak berhati', 'anak yang jujur', 'anak yang tabah',
            'anak-anak palang', 'anak-anak pantai', 'antara bara', 'anugerah gunung', 'asih asah',
            'awan kelabu', 'ayah yang dirindukan', 'ayo bersikap', 'badai mulai', 'bakti sebuah',
            'baktiku untukmu', 'bayang-bayang', 'benang merah', 'berkah di balik', 'berlibur awal',
            'bersatu kita', 'bias-bias', 'bintik-bintik', 'buah tangan', 'budi pekerti',
            'bumi yang subur', 'bunga yang tak', 'cinta kepada', 'cut meuthia', 'cut nyak dien',
            'di antara kecipak', 'di desa kutemukan', 'fajar rachman', 'hukuman bagi', 'iman dan perbuatan',
            'insinyur cilik', 'jalinan kemanusiaan', 'jangan dendam', 'karyawisata ke', 'khalid bin walid',
            'khalifah ali', 'krat-krat mencari', 'laut mengikat', 'maafkan aku', 'mengenal ubi',
            'menggali harta', 'menyibak kabut', 'misteri gadis', 'misteri kota', 'mutiara di telaga',
            'nabi ibrahim', 'nabi musa', 'nabi yusuf', 'nyala bara', 'nyanyian sang', 'oh ibu',
            'pahlawan muda', 'pangeran hidayat', 'perang paregreg', 'perjalanan panjang', 'pertengkaran',
            'pesan pahlawan', 'pesan yang', 'putra banten', 'putra harapan', 'rahasia di balik',
            'raja gedobang', 'rangkai-an cerita', 'sang jupiter', 'sebuah usaha', 'sehalus kain',
            'selembar sutera', 'senyum anak', 'si kecil yang', 'sultan hasanuddin', 'sultan suriansyah',
            'sultan ternate', 'surga di bawah', 'tekad mengentaskan', 'tini dan asih', 'umar bin khatab',
            'api berkobar'
        ];
        
        // Keywords untuk Non-Fiksi berdasarkan data yang ada
        $nonFiksiKeywords = [
            'hadits', 'asmaul husna', 'active learning', 'strategi pembelajaran', 'asyik bermain',
            'ayo belajar', 'ayo bangun', 'ayo mengukur', 'bahaya neoliberalisme', 'bercocok tanam',
            'berkarya selagi', 'bersahabat dengan', 'bertanam tanpa', 'borobudur', 'buaian ibu',
            'cara membuat', 'cara menanam', 'dakwah islamiah', 'dasar-dasar bercocok', 'enam hari',
            'ganesa tanah', 'gangguan penggunaan', 'head strong', 'hutan mangrove', 'ilmu budaya',
            'isme-isme yang', 'kelembagaan', 'kenali paru-paru', 'kesenian daerah', 'kisah-kisah dalam',
            'kisah-kisah jilbab', 'kisah-kisah terpuji', 'kita dan bahasa', 'konflik bersejarah',
            'kumpulan doa', 'kumpulan hadits', 'kumpulan zikir', 'lintasan sejarah', 'logika kaidah',
            'madrasah sejarah', 'majapahit', 'makassar sebagai', 'manajer sukses', 'mari mengenal',
            'masjid masjid', 'masjid tinjauan', 'masuknya islam', 'mekanika terapan', 'membuat bak',
            'membuat gula', 'mendulang intan', 'mengajar dengan', 'mengenal adzan', 'mengenal aqiqah',
            'mengenal jual', 'mengenal pengurusan', 'mengenal penyakit', 'mengenal puasa',
            'mengenal tubuh', 'mengenal wudhu', 'mengenal zakat', 'mengupas ibadah', 'menimba ilmu',
            'menjadi anak yang', 'menyelidiki benda', 'meraih kesuksesan', 'nabi muhammad',
            'nasehat dan ajaran', 'panca indraku', 'pandangan perempuan', 'panduan praktis',
            'pariwisata indonesia', 'pelaku dan politik', 'pencegahan dan penanggulangan',
            'pendidikan islam', 'pengantar filsafat', 'pengantar sejarah', 'pengantar untuk',
            'pengujian dalam', 'peningkatan dan', 'perjalanan sebatang', 'pesona bromo',
            'pola gerak', 'praktik hukum', 'pulau perca', 'raden wijaya', 'remaja industri',
            'riwayat hidup', 'sains di sekitar', 'sejarah khulafaur', 'sejarah pembentukan',
            'sejarah ringkas', 'sekitar walisanga', 'selalu berpikir', 'sendi-sendi ilmu',
            'seribu kenangan', 'strategi membeli', 'sulam payet', 'teladan dari', 'terapi obat',
            'tuanku imam', 'tuanku tambusai', 'udara di sekitar', 'undang-undang piagam',
            'usamah mencari', 'warna-warni', 'waspada bahaya', 'allah tidak', 'amalan di bulan',
            'ayat-ayat pertama', 'ayat-ayat tentang', 'ayo laksanakan', 'belajar shalat',
            'etika bertetangga', 'hadits tentang', 'hidup berhias', 'jauhi riba', 'kumpulan doa',
            'makanan yang', 'menciptakan kedamaian', 'mengenal haji', 'mengenal nama-nama',
            'mengenal shalat', 'mengikuti jejak', 'mengupas surat', 'nama negara', 'nama tempat',
            'nama-nama nabi', 'perempuan dan', 'rumahku surgaku', 'sepintas mengenal',
            'wakaf infaq', 'sehat itu'
        ];
        
        // Cek apakah judul mengandung kata kunci fiksi
        foreach ($fiksiKeywords as $keyword) {
            if (strpos($title, $keyword) !== false) {
                return 'Buku Bacaan - Fiksi';
            }
        }
        
        // Cek apakah judul mengandung kata kunci non-fiksi
        foreach ($nonFiksiKeywords as $keyword) {
            if (strpos($title, $keyword) !== false) {
                return 'Buku Bacaan - Non-Fiksi';
            }
        }
        
        // Default untuk buku bacaan yang tidak jelas
        return 'Buku Bacaan - Non-Fiksi';
    }

    private function determinePaketCategory($title)
    {
        $title = strtolower($title);
        
        // Semua buku paket masuk ke kategori Pendidikan & Pelajaran
        return 'Pendidikan & Pelajaran';
    }

    private function determineKoleksiCategory($jenisKoleksi, $title)
    {
        $jenisKoleksi = strtolower(trim($jenisKoleksi));
        $title = strtolower(trim($title));
        
        // Mapping berdasarkan jenis koleksi dan judul ke kategori baru
        if (strpos($jenisKoleksi, 'kitab') !== false) {
            return 'Agama & Keagamaan';
        }
        
        if (strpos($jenisKoleksi, 'tafsir') !== false) {
            return 'Agama & Keagamaan';
        }
        
        if (strpos($jenisKoleksi, 'metode') !== false || strpos($title, 'metode') !== false) {
            return 'Pendidikan & Pelajaran';
        }
        
        if (strpos($jenisKoleksi, 'kamus') !== false || strpos($title, 'kamus') !== false) {
            return 'Referensi & Kamus';
        }
        
        if (strpos($jenisKoleksi, 'ensiklopedi') !== false || strpos($title, 'ensiklopedi') !== false) {
            return 'Referensi & Kamus';
        }
        
        if (strpos($jenisKoleksi, 'ict') !== false || strpos($title, 'komputer') !== false || strpos($title, 'excel') !== false || strpos($title, 'word') !== false || strpos($title, 'powerpoint') !== false) {
            return 'Teknologi & Sains';
        }
        
        if (strpos($jenisKoleksi, 'buku guru') !== false) {
            return 'Pendidikan & Pelajaran';
        }
        
        if (strpos($jenisKoleksi, 'buku pelajaran') !== false) {
            return 'Pendidikan & Pelajaran';
        }
        
        // Default berdasarkan jenis koleksi
        return ucfirst($jenisKoleksi);
    }

    public function previewExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:csv,txt|max:10240',
            'data_type' => 'required|in:koleksi,bacaan,paket',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        try {
            $file = $request->file('excel_file');
            $dataType = $request->input('data_type');

            // Read CSV file
            $data = [];
            if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $data[] = $row;
                }
                fclose($handle);
            }

            // Get first 10 rows for preview
            $previewData = array_slice($data, 0, 10);

            return response()->json([
                'success' => true,
                'data' => $previewData,
                'total_rows' => count($data)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mapping kategori lama ke kategori baru yang disederhanakan
     */
    private function mapToNewCategory($oldCategory)
    {
        $mapping = [
            // Agama & Keagamaan
            'Agama' => 'Agama & Keagamaan',
            'Kitab' => 'Agama & Keagamaan',
            'Tafsir' => 'Agama & Keagamaan',
            'Pendidikan Kemuhammadiyahan' => 'Agama & Keagamaan',
            'Bahasa Arab' => 'Agama & Keagamaan',
            
            // Pendidikan & Pelajaran
            'Pendidikan' => 'Pendidikan & Pelajaran',
            'Buku Pelajaran' => 'Pendidikan & Pelajaran',
            'Buku Paket' => 'Pendidikan & Pelajaran',
            'Buku Guru' => 'Pendidikan & Pelajaran',
            'Buku siswa' => 'Pendidikan & Pelajaran',
            'Buku pegangan' => 'Pendidikan & Pelajaran',
            'PPKn' => 'Pendidikan & Pelajaran',
            'IPA' => 'Pendidikan & Pelajaran',
            'IPS' => 'Pendidikan & Pelajaran',
            'Bahasa Indonesia' => 'Pendidikan & Pelajaran',
            'Bahasa Inggris' => 'Pendidikan & Pelajaran',
            'Matematika' => 'Pendidikan & Pelajaran',
            'PJOK' => 'Pendidikan & Pelajaran',
            'Seni Budaya' => 'Pendidikan & Pelajaran',
            'Prakarya' => 'Pendidikan & Pelajaran',
            'Bahasa Jawa' => 'Pendidikan & Pelajaran',
            
            // Referensi & Kamus
            'Kamus' => 'Referensi & Kamus',
            'Referensi' => 'Referensi & Kamus',
            'Visual ilmu dan pengetahuan populer' => 'Referensi & Kamus',
            
            // Teknologi & Sains
            'Teknologi' => 'Teknologi & Sains',
            'Sains' => 'Teknologi & Sains',
            
            // Buku Bacaan
            'Fiksi' => 'Buku Bacaan - Fiksi',
            'Non-Fiksi' => 'Buku Bacaan - Non-Fiksi',
            'Buku Bacaan' => 'Buku Bacaan - Non-Fiksi',
            'Novel' => 'Buku Bacaan - Fiksi',
            'Cerita' => 'Buku Bacaan - Fiksi',
            'Sastra & Fiksi' => 'Buku Bacaan - Fiksi',
            
            // Sejarah & Budaya
            'Sejarah' => 'Sejarah & Budaya',
        ];
        
        return $mapping[$oldCategory] ?? 'Lainnya';
    }
}
