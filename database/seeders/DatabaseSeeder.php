<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
        // Mahasiswa kedua
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // Dua mata kuliah lagi (semester berbeda)
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Basis Data',
                'deskripsi' => 'Konsep basis data relasional, normalisasi, dan SQL.',
                'sks' => 3,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Kecerdasan Buatan',
                'deskripsi' => 'Pengantar konsep dan algoritma kecerdasan buatan.',
                'sks' => 2,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // Enrollment (approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);
        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // Dua tugas
        $tugas1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1: Routing & Controller Laravel'],
            [
                'deskripsi' => 'Jelaskan alur request dari route hingga view pada Laravel beserta contohnya.',
                'deadline' => now()->addDays(7),
            ]
        );

        $tugas2 = Assignment::updateOrCreate(
            ['course_id' => $course2->id, 'judul' => 'Tugas 1: Normalisasi Database'],
            [
                'deskripsi' => 'Lakukan normalisasi 1NF sampai 3NF pada tabel transaksi yang diberikan.',
                'deadline' => now()->addDays(10),
            ]
        );

        // Contoh submission: satu sudah dinilai, satu belum
        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Request masuk ke routes/web.php, diteruskan ke controller, lalu controller mengembalikan view Blade.',
                'submitted_at' => now()->subDay(),
                'nilai' => 88,
                'catatan_dosen' => 'Penjelasan sudah baik, tambahkan contoh middleware.',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $tugas1->id, 'user_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Route menerima URL lalu memanggil method controller yang akan me-render view.',
                'submitted_at' => now()->subHours(5),
                'nilai' => null,
                'catatan_dosen' => null,
            ]
        );
    }
}
