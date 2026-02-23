<?php

namespace Database\Seeders;

use \App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        // Mitra::factory(10)->create();
        $partners = [
            [
                'uuid' => '898a74e6-d028-4402-ba1b-8ce589e4a25e',
                'partner_name' => 'Doss',
                'partner_slug' => 'doss',
                'partner_phone' => '083',
                'partner_email' => 'example@gmail.com',
                'partner_description' => '<p>Doss</p>',
                'partner_image' => 'img_partners/01JVSR4H1JPV0NM3VYRS4MWNBQ.png',
                'partner_address' => '<p>Purwakarta</p>',
                'partner_status' => 'Active',
                'created_at' => '2025-05-21 15:42:02',
                'updated_at' => '2025-05-21 15:42:02'
            ],
            [
                'uuid' => '8abf05c3-1a80-48ba-8c6e-0003730258c4',
                'partner_name' => 'Qwords',
                'partner_slug' => 'qwords',
                'partner_phone' => '083',
                'partner_email' => 'example@gmail.com',
                'partner_description' => '<p>qwords</p>',
                'partner_image' => 'img_partners/01JVSQY2E13HNMZQEMWSJWKP32.png',
                'partner_address' => '<p>bandung</p>',
                'partner_status' => 'Active',
                'created_at' => '2025-05-21 15:38:31',
                'updated_at' => '2025-05-21 15:38:31'
            ],
            [
                'uuid' => "95e36459-b818-41ae-b6f0-0fdf73199ff3",
                'partner_name' => "Herblass",
                'partner_slug' => "herblass",
                'partner_phone' => "0813981957060",
                'partner_email' => "sales@herblass.com",
                'partner_description' => "<p>Herblass adalah minuman tradisional asli Indonesia,
berdiri sejak tahun 2005 sudah memiliki legalitas halal, pirt, BPOM dan&nbsp; memil
iki hak paten dengan merek dagang Herblass.</p><p>Herblass berbahan dasar empon - em
pon, kunyit, temulawak, sereh, dan rempah alami kualitas terbaik lainnya. Herblass m
ampu meningkatkan daya tubuh, meningkatkan sistem imunitas sehingga dapat mencegah d
an membantu mengatasi penyakit.</p><p>Minum Herblass setiap hari rasakan respon tubu
h terbaik menjaga kesehatan.</p>",
                'partner_image' => "img_partners/01JVSR6M7QMQY3ZS5SSBWTBT7E.png",
                'partner_address' => "<p>Kabupaten Purwakarta, Jawa Barat - Indonesia 41182</p>",
                'partner_status' => "Active",
                'created_at' => "2025-05-21 15:43:11",
                'updated_at' => "2025-05-21 15:43:11",
            ],
            [
                'uuid' => "b61d8a25-1ad9-4303-b3fc-6825c0cb484a",
                'partner_name' => "Pondok Lensa",
                'partner_slug' => "pondok-lensa",
                'partner_phone' => "083",
                'partner_email' => "example@gmail.com",
                'partner_description' => "<p>Pondok Lensa</p>",
                'partner_image' => "img_partners/01JVSR0H867V7QY3071HJ7BESF.png",
                'partner_address' => "<p>Purwakarta</p>",
                'partner_status' => "Active",
                'created_at' => "2025-05-21 15:39:52",
                'updated_at' => "2025-05-21 15:39:52",
            ],
            [
                'uuid' => "cf3fc649-6917-47ea-bbf5-a3805c32cb42",
                'partner_name' => "TOPUR",
                'partner_slug' => "topur",
                'partner_phone' => "081993390000",
                'partner_email' => "nuparis@gmail.com",
                'partner_description' => "<p>Topur singkatan dari Toko Purwakarta adalah marketp lace yang menyediakan produk dengan promo dan diskon</p>",
                'partner_image' => "img_partners/01JVYNTTHMXVJWN6QN5FCPM62J.png",
                'partner_address' => "<p>Jl. Raya Sadang - Subang Kp. Cimaung, RT.016/RW.004, Ciwangi, Kec. Bungursari, Kabupaten Purwakarta, Jawa Barat 41181</p>",
                'partner_status' => "Active",
                'created_at' => "2025-05-21 15:41:04",
                'updated_at' => "2025-05-23 13:38:42",
            ],
            [
                'uuid' => "d74bf64f-8c7a-4b7e-84c4-fd635748fc2c",
                'partner_name' => "MPP Bale Madukara",
                'partner_slug' => "mpp-bale-madukara",
                'partner_phone' => "081909898111",
                'partner_email' => "mppmadukara@gmail.com",
                'partner_description' => "<p>Mal Pelayanan Publik Bale Madukara memberikan kemudahan dalam mengakses berbagai layanan publik. Untuk informasi lebih lanjut mengenai layanan kami, silakan kunjungi akun Instagram kami di @mppmadukara. Temukan update terbaru dan panduan layanan secara langsung di sana!</p>",
                'partner_image' => "img_partners/01JVZ9XT5X0FPRNGV51JJ4QHNB.png",
                'partner_address' => "<p>Jl. Jendral Sudirman No.Kel, Nagri Kaler, Kec. Purwakarta, Kabupaten Purwakarta, Jawa Barat 41115&nbsp;</p>",
                'partner_status' => "Active",
                'created_at' => "2025-05-23 13:27:55",
                'updated_at' => "2025-05-23 19:29:09",
            ],
        ];

        foreach ($partners as $partner) {
            Partner::create($partner);
        }
    }
}
