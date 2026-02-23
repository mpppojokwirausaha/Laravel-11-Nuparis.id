<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = [
            [
                'uuid' => Str::uuid(),
                'property_name' => 'Apartemen Lavana Residence – Hunian Nyaman di Pusat Kota Jakarta',
                'property_slug' => Str::slug('Apartemen Lavana Residence – Hunian Nyaman di Pusat Kota Jakarta', '-'),
                'property_type' => 'Apartment',
                'property_price' => 700000,
                'property_address' => 'Jl. Prof. Dr. Satrio No.88, Kuningan, Jakarta Selatan (5 menit ke Lotte Shopping Avenue & Mega Kuningan, 10 menit ke Stasiun MRT)',
                'property_description' => 'Lavana Residence adalah apartemen modern yang menggabungkan kenyamanan, gaya hidup urban, dan aksesibilitas tinggi di pusat kota Jakarta. Dirancang dengan arsitektur kontemporer dan fasilitas premium, Lavana Residence menjadi pilihan ideal bagi kaum profesional, keluarga muda, maupun investor properti.  Terletak strategis di kawasan Kuningan, hanya selangkah dari pusat bisnis, perkantoran, pusat perbelanjaan, dan fasilitas kesehatan ternama. Apartemen ini menawarkan berbagai tipe unit mulai dari studio hingga dua kamar tidur dengan tata ruang efisien dan pemandangan kota yang menakjubkan. Nikmati pengalaman hunian yang nyaman dengan beragam fasilitas eksklusif seperti kolam renang rooftop, gym, taman tematik, area komersial, hingga sistem keamanan 24 jam. Lavana Residence menghadirkan kualitas hidup yang lebih baik, tenang, dan terhubung langsung dengan denyut nadi kota.',
                'property_image' => 'img_properties/property-1.jpg',
                'property_building_area' => '80 m2',
                'property_fasilities' => ['Carport, Stove, Refrigerator, Fire Extinguisher, Garasi, Microwave, Gordyn, Telephone'],
                'property_certificate' => ['-'],
            ],
            [
                'uuid' => Str::uuid(),
                'property_name' => 'The Aether Park Apartment',
                'property_slug' => Str::slug('The Aether Park Apartment'),
                'property_type' => 'Apartment',
                'property_price' => 800000,
                'property_address' => 'Jl. BSD Raya Barat No.8, Desa Pagedangan, Kecamatan Pagedangan Kota Tangerang Selatan, Banten 15339 (Area BSD City – dekat AEON Mall & Digital Hub)',
                'property_description' => 'The Aether Park merupakan apartemen eksklusif di kawasan BSD City, Tangerang Selatan, yang menawarkan hunian modern berpadu dengan nuansa hijau dan lingkungan yang tenang. Didesain untuk gaya hidup aktif dan sehat, The Aether Park cocok untuk keluarga muda, profesional, maupun ekspatriat.  Setiap unit dirancang dengan pencahayaan alami, sirkulasi udara optimal, dan sentuhan arsitektur minimalis modern. Lokasinya hanya 7 menit dari AEON Mall dan 10 menit ke Stasiun Rawa Buntu, menjadikan mobilitas penghuni lebih efisien.',
                'property_image' => 'img_properties/property-2.png',
                'property_building_area' => '80 m2',
                'property_fasilities' => ['Carport, Stove, Refrigerator, Fire Extinguisher, Garasi, Microwave, Gordyn, Telephone'],
                'property_certificate' => ['-'],
            ],
            [
                'uuid' => Str::uuid(),
                'property_name' => 'Grand Sagara Apartment',
                'property_slug' => Str::slug('Grand Sagara Apartment'),
                'property_type' => 'Apartment',
                'property_price' => 1200000,
                'property_address' => 'Jl. Pantai Indah Utara 2 No.1, Pantai Indah Kapuk 2, Kecamatan Penjaringan, Jakarta Utara, DKI Jakarta 14470',
                'property_description' => 'Grand Sagara Apartment adalah hunian tepi pantai eksklusif yang terletak di kawasan strategis Pantai Indah Kapuk (PIK) 2, Jakarta Utara. Mengusung konsep “Resort Living by The Sea”, apartemen ini menyajikan suasana tropis yang mewah dengan panorama laut langsung dari balkon unit Anda. Dirancang dengan gaya arsitektur modern tropikal, Grand Sagara menawarkan ketenangan hidup, privasi, dan kenyamanan kelas atas, cocok bagi eksekutif muda, ekspatriat, maupun keluarga mapan.',
                'property_image' => 'img_properties/property-3.jpg',
                'property_building_area' => '80 m2',
                'property_fasilities' => ['Carport, Stove, Refrigerator, Fire Extinguisher, Garasi, Microwave, Gordyn, Telephone'],
                'property_certificate' => ['-'],
            ],
            [
                'uuid' => Str::uuid(),
                'property_name' => 'Arunika City Loft',
                'property_slug' => Str::slug('Arunika City Loft'),
                'property_type' => 'Apartment',
                'property_price' => 600000,
                'property_address' => 'Jl. KH. Noer Ali No.55, Kelurahan Duren Jaya, Kecamatan Bekasi Timur, Kota Bekasi, Jawa Barat 17111 (200 meter dari Stasiun Bekasi Timur, 5 menit dari Tol Bekasi Barat)',
                'property_description' => 'Arunika City Loft adalah apartemen modern minimalis yang berlokasi strategis hanya 200 meter dari Stasiun KRL Bekasi Timur. Dengan konsep Transit-Oriented Development (TOD), apartemen ini dirancang khusus untuk kamu yang mendambakan hunian nyaman, terjangkau, dan dekat transportasi publik.  Cocok bagi pekerja urban, mahasiswa, maupun investor sewa jangka pendek, Arunika menawarkan ruang fungsional dengan harga bersahabat dan fasilitas lengkap.',
                'property_image' => 'img_properties/property-4.jpg',
                'property_building_area' => '80 m2',
                'property_fasilities' => ['Carport, Stove, Refrigerator, Fire Extinguisher, Garasi, Microwave, Gordyn, Telephone'],
                'property_certificate' => ['-'],
            ],
            [
                'uuid' => Str::uuid(),
                'property_name' => 'Verdea Hills Apartment',
                'property_slug' => Str::slug('Verdea Hills Apartment'),
                'property_type' => 'Apartment',
                'property_price' => 1000000,
                'property_address' => 'Jl. Bukit Dago Timur No.27, Dago Atas, Kecamatan Coblong, Kota Bandung, Jawa Barat 40135 (10 menit dari Simpang Dago, 15 menit ke ITB & RS Borromeus)',
                'property_description' => 'Verdea Hills adalah apartemen bernuansa resort tropis yang berlokasi di dataran tinggi Bandung Utara, tepatnya di kawasan Dago Atas. Dikelilingi pepohonan pinus dan udara sejuk pegunungan, Verdea Hills menghadirkan hunian ideal untuk kamu yang menginginkan ketenangan, kenyamanan, dan keindahan alam dalam satu paket.  Setiap unit dirancang dengan bukaan jendela lebar untuk menangkap cahaya alami dan view lanskap hijau yang menyejukkan mata. Verdea Hills cocok untuk keluarga muda, pensiunan, hingga pebisnis properti yang mencari nilai investasi tinggi di Bandung.',
                'property_image' => 'img_properties/property-5.jpg',
                'property_building_area' => '80 m2',
                'property_fasilities' => ['Carport, Stove, Refrigerator, Fire Extinguisher, Garasi, Microwave, Gordyn, Telephone'],
                'property_certificate' => ['-'],
            ],
        ];

        foreach ($properties as $property) {
            Property::updateOrCreate($property);
        }
    }
}
