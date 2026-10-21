<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Mapel;
use App\Models\Soal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@edugames.test'],
            [
                'name' => 'Admin Edu Games',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        // Demo user dengan langganan aktif
        User::updateOrCreate(
            ['email' => 'siswa@example.com'],
            [
                'name' => 'Siswa Demo',
                'password' => 'password',
                'role' => 'user',
                'subscribed_until' => Carbon::now()->addMonth(),
            ]
        );

        // Demo user tanpa langganan
        User::updateOrCreate(
            ['email' => 'free@example.com'],
            [
                'name' => 'Pengguna Free',
                'password' => 'password',
                'role' => 'user',
                'subscribed_until' => null,
            ]
        );

        $this->seedPlans();
        $this->seedData();
    }

    private function seedPlans(): void
    {
        $plans = [
            ['nama' => 'Premium 1 Bulan', 'slug' => 'premium-1-bulan', 'harga' => 50000, 'durasi_bulan' => 1, 'deskripsi' => 'Akses semua game tanpa iklan selama 1 bulan.', 'urutan' => 1],
            ['nama' => 'Premium 3 Bulan', 'slug' => 'premium-3-bulan', 'harga' => 135000, 'durasi_bulan' => 3, 'deskripsi' => 'Akses semua game tanpa iklan selama 3 bulan. Hemat 10%.', 'urutan' => 2],
            ['nama' => 'Premium 12 Bulan', 'slug' => 'premium-12-bulan', 'harga' => 480000, 'durasi_bulan' => 12, 'deskripsi' => 'Akses semua game tanpa iklan selama 1 tahun. Harga paling hemat.', 'urutan' => 3],
            ['nama' => 'Sekolah (Tahunan)', 'slug' => 'sekolah-tahunan', 'harga' => 2500000, 'durasi_bulan' => 12, 'deskripsi' => 'Paket khusus sekolah: kelola banyak akun siswa.', 'urutan' => 4],
        ];

        foreach ($plans as $plan) {
            \App\Models\SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }

    private function seedData(): void
    {
        // ===== MATEMATIKA =====
        $matematika = Mapel::updateOrCreate(
            ['slug' => 'matematika'],
            [
                'nama' => 'Matematika',
                'kelas' => 'SD',
                'deskripsi' => 'Belajar berhitung, penjumlahan, pengurangan, perkalian dan pembagian dengan cara yang seru!',
                'icon' => 'calculator',
                'maskot' => '🦎',
                'warna' => '#4f46e5',
                'urutan' => 1,
            ]
        );

        $gameKuis = Game::updateOrCreate(
            ['slug' => 'matematika-kuis-hitung'],
            [
                'mapel_id' => $matematika->id,
                'nama' => 'Kuis Hitung Cepat',
                'jenis' => 'kuis',
                'deskripsi' => 'Jawab pilihan ganda operasi hitung dengan cepat dan tepat.',
                'icon' => 'calculator',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $gameBenerSalah = Game::updateOrCreate(
            ['slug' => 'matematika-bener-salah'],
            [
                'mapel_id' => $matematika->id,
                'nama' => 'Benar atau Salah?',
                'jenis' => 'bener_salah',
                'deskripsi' => 'Tentukan apakah pernyataan matematika ini benar atau salah.',
                'icon' => 'check-badge',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== IPA =====
        $ipa = Mapel::updateOrCreate(
            ['slug' => 'ipa'],
            [
                'nama' => 'IPA',
                'kelas' => 'SD',
                'deskripsi' => 'Jelajahi tubuh manusia, hewan, tumbuhan dan alam sekitar dengan kuis sains yang menyenangkan.',
                'icon' => 'flask',
                'maskot' => '🐬',
                'warna' => '#059669',
                'urutan' => 2,
            ]
        );

        $gameSains = Game::updateOrCreate(
            ['slug' => 'ipa-kuis-sains'],
            [
                'mapel_id' => $ipa->id,
                'nama' => 'Kuis Sains',
                'jenis' => 'kuis',
                'deskripsi' => 'Uji pengetahuanmu tentang tubuh manusia dan alam sekitar.',
                'icon' => 'flask',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $gameTekaTeki = Game::updateOrCreate(
            ['slug' => 'ipa-teka-teki-jodohkan'],
            [
                'mapel_id' => $ipa->id,
                'nama' => 'Teka-Teki Jodohkan',
                'jenis' => 'teka_teki',
                'deskripsi' => 'Pasangkan hewan atau tumbuhan dengan ciri atau golongan yang tepat.',
                'icon' => 'puzzle',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== MATERI LAINNYA =====
        $materiLain = Mapel::updateOrCreate(
            ['slug' => 'materi-lainnya'],
            [
                'nama' => 'Materi Lainnya',
                'kelas' => 'SD',
                'deskripsi' => 'Kumpulan soal Bahasa Indonesia, IPS dan pengetahuan umum lainnya dalam bentuk permainan yang seru.',
                'icon' => 'book',
                'maskot' => '🐘',
                'warna' => '#d97706',
                'urutan' => 3,
            ]
        );

        $gameBahasa = Game::updateOrCreate(
            ['slug' => 'materi-lain-kuis-bahasa'],
            [
                'mapel_id' => $materiLain->id,
                'nama' => 'Kuis Bahasa Indonesia',
                'jenis' => 'kuis',
                'deskripsi' => 'Uji pemahamanmu tentang ejaan, peribahasa dan kosakata Bahasa Indonesia.',
                'icon' => 'book',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $gameIps = Game::updateOrCreate(
            ['slug' => 'materi-lain-bener-salah-ips'],
            [
                'mapel_id' => $materiLain->id,
                'nama' => 'Benar atau Salah? (IPS)',
                'jenis' => 'bener_salah',
                'deskripsi' => 'Tentukan apakah pernyataan tentang IPS dan pengetahuan umum benar atau salah.',
                'icon' => 'check-badge',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== BAHASA INDONESIA =====
        $bIndo = Mapel::updateOrCreate(
            ['slug' => 'bahasa-indonesia'],
            [
                'nama' => 'Bahasa Indonesia',
                'kelas' => 'SD',
                'deskripsi' => 'Belajar ejaan, kosakata, peribahasa dan pemahaman bacaan Bahasa Indonesia dengan seru.',
                'icon' => 'book',
                'maskot' => '🦜',
                'warna' => '#7c3aed',
                'urutan' => 4,
            ]
        );

        $bIndoKuis = Game::updateOrCreate(
            ['slug' => 'bindo-kuis-bahasa'],
            [
                'mapel_id' => $bIndo->id,
                'nama' => 'Kuis Bahasa Indonesia',
                'jenis' => 'kuis',
                'deskripsi' => 'Uji pengetahuanmu tentang ejaan, sinonim dan peribahasa.',
                'icon' => 'book',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $bIndoTeka = Game::updateOrCreate(
            ['slug' => 'bindo-teka-teki-jodohkan'],
            [
                'mapel_id' => $bIndo->id,
                'nama' => 'Teka-Teki Jodohkan (Antonim)',
                'jenis' => 'teka_teki',
                'deskripsi' => 'Pasangkan kata dengan antonimnya yang tepat.',
                'icon' => 'puzzle',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== IPS =====
        $ips = Mapel::updateOrCreate(
            ['slug' => 'ips'],
            [
                'nama' => 'IPS',
                'kelas' => 'SD',
                'deskripsi' => 'Kenali geografi, sejarah dan pengetahuan sosial Indonesia dalam kuis yang menyenangkan.',
                'icon' => 'globe',
                'maskot' => '🐃',
                'warna' => '#0d9488',
                'urutan' => 5,
            ]
        );

        $ipsKuis = Game::updateOrCreate(
            ['slug' => 'ips-kuis-negara'],
            [
                'mapel_id' => $ips->id,
                'nama' => 'Kuis Indonesia & Dunia',
                'jenis' => 'kuis',
                'deskripsi' => 'Tebak ibu kota, gunung dan pengetahuan geografi Indonesia.',
                'icon' => 'globe',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $ipsBenerSalah = Game::updateOrCreate(
            ['slug' => 'ips-bener-salah'],
            [
                'mapel_id' => $ips->id,
                'nama' => 'Benar atau Salah? (IPS)',
                'jenis' => 'bener_salah',
                'deskripsi' => 'Tentukan benar atau salah pernyataan seputar Indonesia.',
                'icon' => 'check-badge',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== BAHASA INGGRIS =====
        $inggris = Mapel::updateOrCreate(
            ['slug' => 'bahasa-inggris'],
            [
                'nama' => 'Bahasa Inggris',
                'kelas' => 'SD',
                'deskripsi' => 'Perkenalkan kosakata dan kalimat dasar Bahasa Inggris dengan permainan yang seru.',
                'icon' => 'flag',
                'maskot' => '🐯',
                'warna' => '#db2777',
                'urutan' => 6,
            ]
        );

        $inggrisKuis = Game::updateOrCreate(
            ['slug' => 'bing-kuis-kosakata'],
            [
                'mapel_id' => $inggris->id,
                'nama' => 'Kuis Kosakata Inggris',
                'jenis' => 'kuis',
                'deskripsi' => 'Tebak arti kata dan kalimat dasar Bahasa Inggris.',
                'icon' => 'flag',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $inggrisBenerSalah = Game::updateOrCreate(
            ['slug' => 'bing-bener-salah'],
            [
                'mapel_id' => $inggris->id,
                'nama' => 'Benar atau Salah? (Grammar)',
                'jenis' => 'bener_salah',
                'deskripsi' => 'Tentukan benar atau salah penggunaan kalimat bahasa Inggris dasar.',
                'icon' => 'check-badge',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== PKn =====
        $pkn = Mapel::updateOrCreate(
            ['slug' => 'pkn'],
            [
                'nama' => 'PKn',
                'kelas' => 'SD',
                'deskripsi' => 'Kenali Pancasila, lambang negara dan nilai-nilai kewarganegaraan Indonesia.',
                'icon' => 'shield',
                'maskot' => '🦅',
                'warna' => '#dc2626',
                'urutan' => 7,
            ]
        );

        $pknKuis = Game::updateOrCreate(
            ['slug' => 'pkn-kuis-pancasila'],
            [
                'mapel_id' => $pkn->id,
                'nama' => 'Kuis Pancasila & Negara',
                'jenis' => 'kuis',
                'deskripsi' => 'Uji pengetahuanmu tentang Pancasila dan simbol negara.',
                'icon' => 'shield',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $pknBenerSalah = Game::updateOrCreate(
            ['slug' => 'pkn-bener-salah'],
            [
                'mapel_id' => $pkn->id,
                'nama' => 'Benar atau Salah? (Nilai Pancasila)',
                'jenis' => 'bener_salah',
                'deskripsi' => 'Tentukan pernyataan yang sesuai dengan nilai-nilai Pancasila.',
                'icon' => 'check-badge',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== SENI BUDAYA =====
        $seni = Mapel::updateOrCreate(
            ['slug' => 'seni-budaya'],
            [
                'nama' => 'Seni Budaya',
                'kelas' => 'SD',
                'deskripsi' => 'Kenalilah alat musik, tarian dan kesenian daerah Indonesia dengan kuis yang menarik.',
                'icon' => 'music',
                'maskot' => '🦚',
                'warna' => '#ca8a04',
                'urutan' => 8,
            ]
        );

        $seniKuis = Game::updateOrCreate(
            ['slug' => 'seni-kuis-kesenian'],
            [
                'mapel_id' => $seni->id,
                'nama' => 'Kuis Kesenian Daerah',
                'jenis' => 'kuis',
                'deskripsi' => 'Tebak alat musik dan tarian khas Indonesia.',
                'icon' => 'music',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 1,
            ]
        );

        $seniTeka = Game::updateOrCreate(
            ['slug' => 'seni-teka-teki-jodohkan'],
            [
                'mapel_id' => $seni->id,
                'nama' => 'Teka-Teki Jodohkan (Alat Musik)',
                'jenis' => 'teka_teki',
                'deskripsi' => 'Pasangkan nama alat musik dengan daerah asalnya.',
                'icon' => 'puzzle',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 2,
            ]
        );

        // ===== GAME CONTOH JENIS BARU (isian, urutkan, menjodohkan) =====
        $ipaIsian = Game::updateOrCreate(
            ['slug' => 'ipa-isian'],
            [
                'mapel_id' => $ipa->id,
                'nama' => 'Isian Singkat IPA',
                'jenis' => 'isian',
                'deskripsi' => 'Ketik jawabanmu untuk melengkapi kalimat IPA berikut ini.',
                'icon' => 'flask',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 3,
            ]
        );

        $ipsUrutkan = Game::updateOrCreate(
            ['slug' => 'ips-urutkan'],
            [
                'mapel_id' => $ips->id,
                'nama' => 'Urutkan Kejadian',
                'jenis' => 'urutkan',
                'deskripsi' => 'Susun kejadian dan hari sesuai urutan yang benar.',
                'icon' => 'globe',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 3,
            ]
        );

        $cocokHewan = Game::updateOrCreate(
            ['slug' => 'materi-lain-cocok-hewan'],
            [
                'mapel_id' => $materiLain->id,
                'nama' => 'Jodohkan Gambar Hewan',
                'jenis' => 'cocok_gambar',
                'deskripsi' => 'Pasangkan gambar hewan dengan namanya.',
                'icon' => 'puzzle',
                'durasi_menit' => 5,
                'poin_per_benar' => 10,
                'urutan' => 3,
            ]
        );

        $this->seedSoalKuisMatematika($gameKuis);
        $this->seedSoalBenerSalahMatematika($gameBenerSalah);
        $this->seedSoalKuisIpa($gameSains);
        $this->seedSoalTekaTekiIpa($gameTekaTeki);
        $this->seedSoalKuisBahasa($gameBahasa);
        $this->seedSoalBenerSalahIps($gameIps);
        $this->seedSoalKuisBindo($bIndoKuis);
        $this->seedSoalTekaAntonim($bIndoTeka);
        $this->seedSoalKuisIps($ipsKuis);
        $this->seedSoalBenerSalahIpsBaru($ipsBenerSalah);
        $this->seedSoalKuisInggris($inggrisKuis);
        $this->seedSoalBenerSalahInggris($inggrisBenerSalah);
        $this->seedSoalKuisPkn($pknKuis);
        $this->seedSoalBenerSalahPkn($pknBenerSalah);
        $this->seedSoalKuisSeni($seniKuis);
        $this->seedSoalTekaSeni($seniTeka);
        $this->seedSoalIsianIpa($ipaIsian);
        $this->seedSoalUrutkanIps($ipsUrutkan);
        $this->seedSoalCocokHewan($cocokHewan);
    }

    private function seedSoalKuisMatematika(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Hasil dari 7 + 8 adalah ...', 'pilihan' => ['14', '15', '16', '17'], 'jawaban' => '15', 'penjelasan' => '7 + 8 = 15', 'urutan' => 1],
            ['pertanyaan' => 'Hasil dari 15 - 6 adalah ...', 'pilihan' => ['9', '8', '10', '11'], 'jawaban' => '9', 'penjelasan' => '15 - 6 = 9', 'urutan' => 2],
            ['pertanyaan' => 'Hasil dari 4 x 6 adalah ...', 'pilihan' => ['20', '22', '24', '26'], 'jawaban' => '24', 'penjelasan' => '4 x 6 = 24', 'urutan' => 3],
            ['pertanyaan' => 'Hasil dari 36 : 4 adalah ...', 'pilihan' => ['6', '7', '8', '9'], 'jawaban' => '9', 'penjelasan' => '36 : 4 = 9', 'urutan' => 4],
            ['pertanyaan' => 'Hasil dari 12 + 9 adalah ...', 'pilihan' => ['20', '21', '22', '23'], 'jawaban' => '21', 'penjelasan' => '12 + 9 = 21', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalBenerSalahMatematika(Game $game): void
    {
        $soal = [
            ['pertanyaan' => '9 + 3 = 12', 'jawaban' => 'Benar', 'penjelasan' => '9 + 3 memang sama dengan 12', 'urutan' => 1],
            ['pertanyaan' => '20 - 5 = 14', 'jawaban' => 'Salah', 'penjelasan' => '20 - 5 = 15, bukan 14', 'urutan' => 2],
            ['pertanyaan' => '7 x 3 = 21', 'jawaban' => 'Benar', 'penjelasan' => '7 x 3 = 21', 'urutan' => 3],
            ['pertanyaan' => '50 : 5 = 12', 'jawaban' => 'Salah', 'penjelasan' => '50 : 5 = 10, bukan 12', 'urutan' => 4],
            ['pertanyaan' => '100 lebih besar dari 90', 'jawaban' => 'Benar', 'penjelasan' => '100 > 90, benar', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisIpa(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Organ tubuh yang berfungsi memompa darah adalah ...', 'pilihan' => ['Paru-paru', 'Jantung', 'Hati', 'Ginjal'], 'jawaban' => 'Jantung', 'penjelasan' => 'Jantung memompa darah ke seluruh tubuh', 'urutan' => 1],
            ['pertanyaan' => 'Tumbuhan membuat makanannya sendiri melalui proses ...', 'pilihan' => ['Fotosintesis', 'Respirasi', 'Fertilisasi', 'Evaporasi'], 'jawaban' => 'Fotosintesis', 'penjelasan' => 'Fotosintesis menghasilkan makanan menggunakan cahaya matahari', 'urutan' => 2],
            ['pertanyaan' => 'Hewan yang menyusui anaknya disebut ...', 'pilihan' => ['Reptil', 'Amfibi', 'Mamalia', 'Burung'], 'jawaban' => 'Mamalia', 'penjelasan' => 'Mamalia menyusui anaknya, contohnya kucing dan sapi', 'urutan' => 3],
            ['pertanyaan' => 'Zat yang kita butuhkan untuk bernapas adalah ...', 'pilihan' => ['Karbon dioksida', 'Oksigen', 'Nitrogen', 'Helium'], 'jawaban' => 'Oksigen', 'penjelasan' => 'Tubuh membutuhkan oksigen untuk bernapas', 'urutan' => 4],
            ['pertanyaan' => 'Alat indera untuk melihat adalah ...', 'pilihan' => ['Telinga', 'Hidung', 'Mata', 'Kulit'], 'jawaban' => 'Mata', 'penjelasan' => 'Mata adalah alat indera penglihatan', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisBahasa(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Kalimat yang benar sesuai EYD adalah ...', 'pilihan' => ['Saya pergi ke pasar', 'saya pergi Ke Pasar', 'Saya pergi Ke pasar', 'saya Pergi ke pasar'], 'jawaban' => 'Saya pergi ke pasar', 'penjelasan' => 'Huruf kapital dipakai di awal kalimat dan nama orang', 'urutan' => 1],
            ['pertanyaan' => 'Sinonim dari kata "pintar" adalah ...', 'pilihan' => ['Bodoh', 'Cerdas', 'Malas', 'Kuat'], 'jawaban' => 'Cerdas', 'penjelasan' => 'Sinonim pintar adalah cerdas', 'urutan' => 2],
            ['pertanyaan' => '"Besar kepala" artinya ...', 'pilihan' => ['Memiliki kepala besar', 'Sombong / angkuh', 'Pandai sekali', 'Kebingungan'], 'jawaban' => 'Sombong / angkuh', 'penjelasan' => 'Peribahasa "besar kepala" berarti sombong', 'urutan' => 3],
            ['pertanyaan' => 'Kata baku yang benar adalah ...', 'pilihan' => ['apotik', 'aktifitas', 'jadwal', 'nasehat'], 'jawaban' => 'jadwal', 'penjelasan' => 'Penulisan baku: apotek, aktivitas, jadwal, nasihat', 'urutan' => 4],
            ['pertanyaan' => 'Antonim dari kata "rajin" adalah ...', 'pilihan' => ['Malas', 'Pandai', 'Tekun', 'Giat'], 'jawaban' => 'Malas', 'penjelasan' => 'Antonim rajin adalah malas', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalBenerSalahIps(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Ibu kota Indonesia adalah Jakarta', 'jawaban' => 'Benar', 'penjelasan' => 'Jakarta adalah ibu kota Indonesia', 'urutan' => 1],
            ['pertanyaan' => 'Gunung tertinggi di Indonesia adalah Gunung Semeru', 'jawaban' => 'Salah', 'penjelasan' => 'Yang tertinggi adalah Puncak Jaya di Papua', 'urutan' => 2],
            ['pertanyaan' => 'Indonesia terletak di Asia Tenggara', 'jawaban' => 'Benar', 'penjelasan' => 'Indonesia berada di kawasan Asia Tenggara', 'urutan' => 3],
            ['pertanyaan' => 'Garis khatulistiwa melintasi Indonesia', 'jawaban' => 'Benar', 'penjelasan' => 'Khatulistiwa melintasi Indonesia (Kota Pontianak)', 'urutan' => 4],
            ['pertanyaan' => 'Bendera Indonesia memiliki tiga warna', 'jawaban' => 'Salah', 'penjelasan' => 'Sang Saka Merah Putih hanya dua warna', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalTekaTekiIpa(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Kucing', 'jawaban' => 'Hewan karnivora / pemakan daging', 'penjelasan' => 'Kucing termasuk karnivora', 'urutan' => 1],
            ['pertanyaan' => 'Sapi', 'jawaban' => 'Hewan herbivora / pemakan tumbuhan', 'penjelasan' => 'Sapi termasuk herbivora', 'urutan' => 2],
            ['pertanyaan' => 'Elang', 'jawaban' => 'Hewan karnivora / pemakan daging', 'penjelasan' => 'Elang termasuk karnivora', 'urutan' => 3],
            ['pertanyaan' => 'Ayam', 'jawaban' => 'Hewan omnivora / pemakan segala', 'penjelasan' => 'Ayam termasuk omnivora', 'urutan' => 4],
            ['pertanyaan' => 'Kambing', 'jawaban' => 'Hewan herbivora / pemakan tumbuhan', 'penjelasan' => 'Kambing termasuk herbivora', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisBindo(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Kalimat tanya yang tepat untuk menanyakan "tempat" adalah ...', 'pilihan' => ['Di mana', 'Kapan', 'Siapa', 'Mengapa'], 'jawaban' => 'Di mana', 'penjelasan' => 'Kata tanya "di mana" menanyakan tempat', 'urutan' => 1],
            ['pertanyaan' => '"Habis Gelap Terbitlah Terang" adalah contoh ...', 'pilihan' => ['Peribahasa', 'Pantun', 'Puisi', 'Hikayat'], 'jawaban' => 'Peribahasa', 'penjelasan' => 'Itu sebuah peribahasa yang bermakna setelah kesulitan datang kebahagiaan', 'urutan' => 2],
            ['pertanyaan' => 'Sinonim dari kata "indah" adalah ...', 'pilihan' => ['Buruk', 'Cantik', 'Besar', 'Kecil'], 'jawaban' => 'Cantik', 'penjelasan' => 'Sinonim indah antara lain cantik', 'urutan' => 3],
            ['pertanyaan' => 'Huruf kapital dipakai pada ...', 'pilihan' => ['awal kalimat dan nama orang', 'akhir kalimat', 'tengah kata', 'semua huruf'], 'jawaban' => 'awal kalimat dan nama orang', 'penjelasan' => 'Huruf kapital dipakai di awal kalimat dan untuk nama orang/tempat', 'urutan' => 4],
            ['pertanyaan' => '"Kepala dingin" berarti ...', 'pilihan' => ['Sakit kepala', 'Tenang dan sabar', 'Lambat berfikir', 'Cepat marah'], 'jawaban' => 'Tenang dan sabar', 'penjelasan' => 'Peribahasa "kepala dingin" berarti tenang dan sabar', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalTekaAntonim(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Pintar', 'jawaban' => 'Bodoh', 'penjelasan' => 'Antonim pintar adalah bodoh', 'urutan' => 1],
            ['pertanyaan' => 'Rajin', 'jawaban' => 'Malas', 'penjelasan' => 'Antonim rajin adalah malas', 'urutan' => 2],
            ['pertanyaan' => 'Indah', 'jawaban' => 'Buruk', 'penjelasan' => 'Antonim indah adalah buruk', 'urutan' => 3],
            ['pertanyaan' => 'Tinggi', 'jawaban' => 'Rendah', 'penjelasan' => 'Antonim tinggi adalah rendah', 'urutan' => 4],
            ['pertanyaan' => 'Cepat', 'jawaban' => 'Lambat', 'penjelasan' => 'Antonim cepat adalah lambat', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisIps(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Ibu kota provinsi Jawa Barat adalah ...', 'pilihan' => ['Bandung', 'Jakarta', 'Semarang', 'Surabaya'], 'jawaban' => 'Bandung', 'penjelasan' => 'Ibu kota Jawa Barat adalah Bandung', 'urutan' => 1],
            ['pertanyaan' => 'Samudra yang membatasi Indonesia di sebelah barat adalah ...', 'pilihan' => ['Samudra Hindia', 'Samudra Pasifik', 'Samudra Atlantik', 'Laut Jawa'], 'jawaban' => 'Samudra Hindia', 'penjelasan' => 'Sebelah barat Indonesia berbatasan dengan Samudra Hindia', 'urutan' => 2],
            ['pertanyaan' => 'Provinsi paling timur Indonesia adalah ...', 'pilihan' => ['Papua', 'Maluku', 'Sulawesi', 'NTT'], 'jawaban' => 'Papua', 'penjelasan' => 'Papua adalah provinsi paling timur Indonesia', 'urutan' => 3],
            ['pertanyaan' => 'Danau terbesar di Indonesia adalah ...', 'pilihan' => ['Danau Toba', 'Danau Maninjau', 'Danau Singkarak', 'Danau Sentani'], 'jawaban' => 'Danau Toba', 'penjelasan' => 'Danau Toba di Sumatera Utara adalah danau terbesar di Indonesia', 'urutan' => 4],
            ['pertanyaan' => 'Sungai terpanjang di Indonesia adalah ...', 'pilihan' => ['Sungai Kapuas', 'Sungai Mahakam', 'Sungai Musi', 'Sungai Bengawan Solo'], 'jawaban' => 'Sungai Kapuas', 'penjelasan' => 'Sungai Kapuas di Kalimantan adalah sungai terpanjang di Indonesia', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalBenerSalahIpsBaru(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Indonesia memiliki lebih dari 17.000 pulau', 'jawaban' => 'Benar', 'penjelasan' => 'Indonesia dikenal sebagai negara kepulauan dengan ribuan pulau', 'urutan' => 1],
            ['pertanyaan' => 'Pulau Kalimantan terbagi menjadi dua provinsi saja', 'jawaban' => 'Salah', 'penjelasan' => 'Kalimantan terbagi menjadi lima provinsi', 'urutan' => 2],
            ['pertanyaan' => 'Sunda Kelapa adalah nama lama kota Jakarta', 'jawaban' => 'Benar', 'penjelasan' => 'Nama lama Jakarta antara lain Sunda Kelapa dan Batavia', 'urutan' => 3],
            ['pertanyaan' => 'Gunung Bromo terletak di Pulau Bali', 'jawaban' => 'Salah', 'penjelasan' => 'Gunung Bromo terletak di Jawa Timur', 'urutan' => 4],
            ['pertanyaan' => 'Negara tetangga Indonesia yang terdekat adalah Singapura dan Malaysia', 'jawaban' => 'Benar', 'penjelasan' => 'Indonesia berbatasan darat dengan Malaysia, Papua Nugini dan Timor Leste', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisInggris(Game $game): void
    {
        $soal = [
            ['pertanyaan' => '"Apple" dalam Bahasa Indonesia berarti ...', 'pilihan' => ['Jeruk', 'Apel', 'Mangga', 'Pisang'], 'jawaban' => 'Apel', 'penjelasan' => '"Apple" artinya apel', 'urutan' => 1],
            ['pertanyaan' => 'Kata yang tepat untuk "Selamat pagi" adalah ...', 'pilihan' => ['Good afternoon', 'Good morning', 'Good night', 'Good evening'], 'jawaban' => 'Good morning', 'penjelasan' => '"Good morning" berarti selamat pagi', 'urutan' => 2],
            ['pertanyaan' => '"Book" berarti ...', 'pilihan' => ['Buku', 'Pensil', 'Pulpen', 'Penggaris'], 'jawaban' => 'Buku', 'penjelasan' => '"Book" berarti buku', 'urutan' => 3],
            ['pertanyaan' => 'Angka "seventeen" dalam angka adalah ...', 'pilihan' => ['15', '16', '17', '18'], 'jawaban' => '17', 'penjelasan' => '"Seventeen" berarti tujuh belas (17)', 'urutan' => 4],
            ['pertanyaan' => '"I am happy" berarti ...', 'pilihan' => ['Aku sedih', 'Aku senang/bahagia', 'Aku lelah', 'Aku lapar'], 'jawaban' => 'Aku senang/bahagia', 'penjelasan' => '"Happy" berarti senang/bahagia', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalBenerSalahInggris(Game $game): void
    {
        $soal = [
            ['pertanyaan' => '"She go to school" adalah kalimat yang benar', 'jawaban' => 'Salah', 'penjelasan' => 'Seharusnya "She goes to school"', 'urutan' => 1],
            ['pertanyaan' => '"A cat" berarti "seekor kucing"', 'jawaban' => 'Benar', 'penjelasan' => '"A cat" berarti seekor kucing', 'urutan' => 2],
            ['pertanyaan' => '"Thank you" berarti "terima kasih"', 'jawaban' => 'Benar', 'penjelasan' => '"Thank you" berarti terima kasih', 'urutan' => 3],
            ['pertanyaan' => 'Kata "red" dalam Bahasa Indonesia berarti "kuning"', 'jawaban' => 'Salah', 'penjelasan' => '"Red" berarti merah, bukan kuning', 'urutan' => 4],
            ['pertanyaan' => '"Hello" adalah sapaan untuk menyapa', 'jawaban' => 'Benar', 'penjelasan' => '"Hello" dipakai untuk menyapa', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisPkn(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Dasar negara Indonesia adalah ...', 'pilihan' => ['Pancasila', 'UUD 1945', 'Bhinneka Tunggal Ika', 'NKRI'], 'jawaban' => 'Pancasila', 'penjelasan' => 'Pancasila adalah dasar negara Indonesia', 'urutan' => 1],
            ['pertanyaan' => 'Sila pertama Pancasila berbunyi ...', 'pilihan' => ['Kemanusiaan yang adil dan beradab', 'Ketuhanan Yang Maha Esa', 'Persatuan Indonesia', 'Keadilan sosial'], 'jawaban' => 'Ketuhanan Yang Maha Esa', 'penjelasan' => 'Sila pertama adalah Ketuhanan Yang Maha Esa', 'urutan' => 2],
            ['pertanyaan' => 'Lambang sila kedua Pancasila adalah ...', 'pilihan' => ['Bintang', 'Rantai', 'Pohon beringin', 'Padi dan kapas'], 'jawaban' => 'Rantai', 'penjelasan' => 'Rantai adalah lambang sila kedua (kemanusiaan)', 'urutan' => 3],
            ['pertanyaan' => 'Burung yang menjadi lambang negara Indonesia adalah ...', 'pilihan' => ['Burung Cendrawasih', 'Burung Garuda', 'Burung Elang', 'Burung Merak'], 'jawaban' => 'Burung Garuda', 'penjelasan' => 'Lambang negara Indonesia adalah Garuda Pancasila', 'urutan' => 4],
            ['pertanyaan' => 'Semboyan bangsa Indonesia adalah ...', 'pilihan' => ['Pancasila', 'Bhinneka Tunggal Ika', 'Merdeka', 'Gotong Royong'], 'jawaban' => 'Bhinneka Tunggal Ika', 'penjelasan' => 'Bhinneka Tunggal Ika berarti berbeda-beda tetapi tetap satu', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalBenerSalahPkn(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Gotong royong adalah contoh nilai Pancasila', 'jawaban' => 'Benar', 'penjelasan' => 'Gotong royong mencerminkan nilai persatuan dan kemanusiaan', 'urutan' => 1],
            ['pertanyaan' => 'Pancasila hanya terdiri dari tiga sila', 'jawaban' => 'Salah', 'penjelasan' => 'Pancasila terdiri dari lima sila', 'urutan' => 2],
            ['pertanyaan' => 'Menghormati teman yang berbeda agama sesuai dengan sila pertama', 'jawaban' => 'Benar', 'penjelasan' => 'Menghormati perbedaan agama adalah pengamalan sila pertama', 'urutan' => 3],
            ['pertanyaan' => 'Bermusyawarah untuk mengambil keputusan sesuai sila keempat', 'jawaban' => 'Benar', 'penjelasan' => 'Musyawarah adalah pengamalan sila keempat', 'urutan' => 4],
            ['pertanyaan' => 'Lambang sila kelima adalah pohon beringin', 'jawaban' => 'Salah', 'penjelasan' => 'Lambang sila kelima adalah padi dan kapas', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalKuisSeni(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Angklung adalah alat musik yang berasal dari ...', 'pilihan' => ['Jawa Barat', 'Bali', 'Sumatera', 'Papua'], 'jawaban' => 'Jawa Barat', 'penjelasan' => 'Angklung berasal dari Jawa Barat', 'urutan' => 1],
            ['pertanyaan' => 'Tari Saman berasal dari daerah ...', 'pilihan' => ['Aceh', 'Bali', 'Sulawesi', 'Papua'], 'jawaban' => 'Aceh', 'penjelasan' => 'Tari Saman berasal dari Aceh', 'urutan' => 2],
            ['pertanyaan' => 'Alat musik yang dimainkan dengan cara ditiup adalah ...', 'pilihan' => ['Gitar', 'Suling', 'Gendang', 'Angklung'], 'jawaban' => 'Suling', 'penjelasan' => 'Suling dimainkan dengan cara ditiup', 'urutan' => 3],
            ['pertanyaan' => 'Gamelan adalah alat musik tradisional dari ...', 'pilihan' => ['Jawa dan Bali', 'Sumatera', 'Kalimantan', 'Sulawesi'], 'jawaban' => 'Jawa dan Bali', 'penjelasan' => 'Gamelan berasal dari Jawa dan Bali', 'urutan' => 4],
            ['pertanyaan' => 'Tari Kecak berasal dari ...', 'pilihan' => ['Bali', 'Jawa Timur', 'Aceh', 'Papua'], 'jawaban' => 'Bali', 'penjelasan' => 'Tari Kecak berasal dari Bali', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['pilihan'], 'jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalTekaSeni(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Angklung', 'jawaban' => 'Jawa Barat', 'penjelasan' => 'Angklung berasal dari Jawa Barat', 'urutan' => 1],
            ['pertanyaan' => 'Tari Saman', 'jawaban' => 'Aceh', 'penjelasan' => 'Tari Saman berasal dari Aceh', 'urutan' => 2],
            ['pertanyaan' => 'Tari Kecak', 'jawaban' => 'Bali', 'penjelasan' => 'Tari Kecak berasal dari Bali', 'urutan' => 3],
            ['pertanyaan' => 'Alat musik Sasando', 'jawaban' => 'Nusa Tenggara Timur', 'penjelasan' => 'Sasando berasal dari NTT', 'urutan' => 4],
            ['pertanyaan' => 'Tari Piring', 'jawaban' => 'Sumatera Barat', 'penjelasan' => 'Tari Piring berasal dari Sumatera Barat', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalIsianIpa(Game $game): void
    {
        $soal = [
            ['pertanyaan' => 'Alat pernapasan pada manusia adalah ....', 'jawaban' => 'paru-paru|paru paru', 'penjelasan' => 'Paru-paru mengambil oksigen dari udara yang kita hirup', 'urutan' => 1],
            ['pertanyaan' => 'Tumbuhan membuat makanannya sendiri melalui proses ....', 'jawaban' => 'fotosintesis', 'penjelasan' => 'Fotosintesis memanfaatkan cahaya matahari, air, dan karbon dioksida', 'urutan' => 2],
            ['pertanyaan' => 'Zat yang dibutuhkan tubuh untuk bernapas adalah ....', 'jawaban' => 'oksigen', 'penjelasan' => 'Kita memerlukan oksigen untuk bernapas', 'urutan' => 3],
            ['pertanyaan' => 'Alat indera untuk melihat adalah ....', 'jawaban' => 'mata', 'penjelasan' => 'Mata adalah alat indera penglihatan', 'urutan' => 4],
            ['pertanyaan' => 'Hewan yang menyusui anaknya disebut ....', 'jawaban' => 'mamalia', 'penjelasan' => 'Contoh mamalia: kucing, sapi, dan manusia', 'urutan' => 5],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['jawaban_benar' => $s['jawaban'], 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalUrutkanIps(Game $game): void
    {
        $presiden = ['Soekarno', 'Suharto', 'B.J. Habibie', 'Abdurrahman Wahid'];
        $kronologi = ['Sumpah Pemuda (1928)', 'Pembentukan PPKI (7 Agustus 1945)', 'Proklamasi Kemerdekaan (17 Agustus 1945)', 'Pengakuan Kedaulatan RI (27 Desember 1949)'];
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $soal = [
            ['pertanyaan' => 'Urutkan presiden Indonesia yang pertama sampai keempat.', 'urutan_benar' => $presiden, 'penjelasan' => 'Urutan presiden: Soekarno, Suharto, B.J. Habibie, lalu Abdurrahman Wahid', 'urutan' => 1],
            ['pertanyaan' => 'Urutkan kejadian berikut dari yang paling awal.', 'urutan_benar' => $kronologi, 'penjelasan' => 'Sumpah Pemuda 1928 mendahului peristiwa Agustus 1945, baru diakui dunia pada 1949', 'urutan' => 2],
            ['pertanyaan' => 'Urutkan hari kerja dari awal minggu.', 'urutan_benar' => $hari, 'penjelasan' => 'Minggu adalah hari pertama, jadi hari kerja dimulai dari Senin', 'urutan' => 3],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $s['urutan_benar'], 'jawaban_benar' => implode(' → ', $s['urutan_benar']), 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }

    private function seedSoalCocokHewan(Game $game): void
    {
        $pasangan = [
            ['kiri' => '🐘', 'kanan' => 'Gajah'],
            ['kiri' => '🦒', 'kanan' => 'Jerapah'],
            ['kiri' => '🐧', 'kanan' => 'Pinguin'],
            ['kiri' => '🐬', 'kanan' => 'Lumba-lumba'],
        ];

        $soal = [
            ['pertanyaan' => 'Jodohkan gambar hewan dengan namanya.', 'penjelasan' => 'Gajah hewan darat terbesar, jerapah terkenal dengan leher panjangnya, pinguin hidup di daerah dingin, dan lumba-lumba hidup di laut', 'urutan' => 1],
            ['pertanyaan' => 'Pasangkan setiap gambar dengan nama hewan yang tepat.', 'penjelasan' => 'Semua hewan ini bisa kamu temukan di kebun binatang', 'urutan' => 2],
        ];

        foreach ($soal as $s) {
            Soal::updateOrCreate(
                ['game_id' => $game->id, 'pertanyaan' => $s['pertanyaan']],
                ['pilihan' => $pasangan, 'jawaban_benar' => implode(', ', array_map(fn ($p) => $p['kiri'].' = '.$p['kanan'], $pasangan)), 'penjelasan' => $s['penjelasan'], 'urutan' => $s['urutan']]
            );
        }
    }
}
