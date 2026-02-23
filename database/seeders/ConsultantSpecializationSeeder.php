<?php

namespace Database\Seeders;

use App\Models\ConsultantSpecialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsultantSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consultantSpecialization = [
            [
                'uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c',
                'consultant_specialization_slug' => 'bidang1',
                'consultant_specialization_name' => 'bidang1'
            ],
            [
                'uuid' => '631266aa-dcd9-46ca-857b-43128d46edbd',
                'consultant_specialization_slug' => 'bidang2',
                'consultant_specialization_name' => 'bidang2'
            ],
            [
                'uuid' => '84873adf-4d0a-4265-b111-8a86c92ae910',
                'consultant_specialization_slug' => 'bidang3',
                'consultant_specialization_name' => 'bidang3'
            ],
        ];

        foreach ($consultantSpecialization as $data) {
            $model = new ConsultantSpecialization($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
