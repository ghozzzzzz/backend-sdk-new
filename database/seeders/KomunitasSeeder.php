<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Komunitas;
use Illuminate\Support\Facades\Hash;

class KomunitasSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/komunitas.csv');

        if (!file_exists($filePath)) {
            throw new \Exception("File CSV tidak ditemukan di: {$filePath}");
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file, 0, ';');

        if ($header === false || empty($header)) {
            throw new \Exception("Header CSV tidak valid atau file kosong.");
        }

        $header = array_map(function ($value) {
            return preg_replace('/^[\x{FEFF}]+/u', '', $value);
        }, $header);

        dump($header);

        $headerMapping = [
            'Id Komunitas' => 'id_komunitas',
            'Nama Komunitas' => 'nama_komunitas',
            'Tipe' => 'tipe',
            'Koordinator' => 'koordinator',
            'Telepon' => 'telepon',
            'Email Komunitas' => 'email_komunitas',
            'Jumlah Anggota' => 'jumlah_anggota',
            'password' => 'password',
        ];

        foreach ($headerMapping as $csvHeader => $dbColumn) {
            if (!in_array($csvHeader, $header)) {
                throw new \Exception("Kolom '$csvHeader' tidak ditemukan di header CSV. Header yang ditemukan: " . implode(', ', $header));
            }
        }

        while ($row = fgetcsv($file, 0, ';')) {
            if (count($row) !== count($header)) {
                throw new \Exception("Jumlah kolom di baris tidak sesuai dengan header. Header: " . count($header) . ", Baris: " . count($row));
            }

            $data = [];
            foreach ($header as $index => $csvHeader) {
                $dbColumn = $headerMapping[$csvHeader];
                $data[$dbColumn] = $row[$index];
            }

            dump($data);

            $telepon = $data['telepon'];
            if (!empty($telepon) && str_starts_with($telepon, '8')) {
                $telepon = '0' . $telepon;
            }

            $jumlahAnggota = $data['jumlah_anggota'] === '' ? null : (int) $data['jumlah_anggota'];

            // Konversi email_komunitas kosong menjadi NULL
            $emailKomunitas = $data['email_komunitas'] === '' ? null : $data['email_komunitas'];

            $password = $data['password'] ?: 'sdkindigo123';

            Komunitas::create([
                'id_komunitas' => $data['id_komunitas'],
                'nama_komunitas' => $data['nama_komunitas'],
                'tipe' => $data['tipe'],
                'koordinator' => $data['koordinator'],
                'telepon' => $telepon,
                'email_komunitas' => $emailKomunitas,
                'jumlah_anggota' => $jumlahAnggota,
                'password' => Hash::make($password),
            ]);
        }

        fclose($file);
    }
}