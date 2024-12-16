<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanDosenSeeder extends Seeder
{
    public function run()
    {
        $kegiatanDosen = [
            // Keterlibatan dosen di JTI Play IT
            [
                'kegiatan_id' => 1,
                'user_id' => 42, 
                'jabatan' => 'PIC',
                'skor' => 5.0,
                'deadline' => '2024-12-31 23:59:59', 
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kegiatan_id' => 1,
                'user_id' => 48,
                'jabatan' => 'Sekretaris', 
                'skor' => 4.0,
                'deadline' => '2024-12-31 23:59:59', 
                'created_at' => now(),
                'updated_at' => now()
            ],

            
            [
                'kegiatan_id' => 2,
                'user_id' => 53, 
                'jabatan' => 'PIC',
                'skor' => 3.0,
                'deadline' => '2024-12-31 23:59:59', 
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        DB::table('t_kegiatan_dosen')->insert($kegiatanDosen);
    }
}