<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'uuid' => '631266aa-dcd9-46ca-857b-43128d46edbd',
                'ticket_status_name' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => '2ff4c17d-64ad-4db9-abd8-9e300cb1dbda',
                'ticket_status_name' => 'open',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'uuid' => '1768c562-62a0-41e0-8f6d-76f5ec5b5e7c',
                'ticket_status_name' => 'close',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($data as $data) {
            $model = new TicketStatus($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
