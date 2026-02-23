<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news = [
            [
                'uuid' => '015d96f0-ec10-4aaf-ac5a-0cd30e4dced6',
                'news_fullname' => 'jabar.tribunnews.com',
                'news_slug' => 'httpsjabartribunnewscom20201027bantu-umkm-terdampak-pandemi-warga-purwakarta-ciptakan-aplikasi-topur',
                'news_url' => 'https://jabar.tribunnews.com/2020/10/27/bantu-umkm-terdampak-pandemi-warga-purwakarta-ciptakan-aplikasi-topur.html',
                'news_image' => 'img_news/01JVZB3Z378Y69Z5429DV9CTXE.png',
                'news_avatar' => 'img_news/01JVYQJY807E3DBR5T2ZSR1G6Q.png',
                'news_content' => '<p>Bantu UMKM Terdampak Pandemi, Warga Purwakarta Ciptakan Aplikasi TOPUR</p>',
                'created_at' => '2025-05-23 21:08:38',
                'updated_at' => '2025-05-24 02:49:59'
            ],
            [
                'uuid' => '16787678-29e1-45ee-bd84-31c9cbdac845',
                'news_fullname' => 'pikiran-rakyat.com',
                'news_slug' => 'httpswwwpikiran-rakyatcomjawa-baratpr-01356228topur-e-commerce-lokal-purwakarta-siap-bantu-umkm-di-tengah-wabah-virus-corona-covid-19pageall',
                'news_url' => 'https://www.pikiran-rakyat.com/jawa-barat/pr-01356228/topur-e-commerce-lokal-purwakarta-siap-bantu-umkm-di-tengah-wabah-virus-corona-covid-19?page=all',
                'news_image' => 'img_news/01JVZCAZJHFW8K3NP5Z120BW72.webp',
                'news_avatar' => 'img_news/01JVYQ5T6RAE32AX4RDER25H1K.png',
                'news_content' => '<p>Topur, E-commerce Lokal Purwakarta Siap Bantu UMKM di Tengah Wabah Virus Corona (Covid-19)</p>',
                'created_at' => '2025-05-23 21:01:28',
                'updated_at' => '2025-05-24 03:11:18',
            ],
            [
                'uuid' => '260984c3-2eee-41e8-bfe6-c958d04e639c',
                'news_fullname' => 'Republika.co.id',
                'news_slug' =>
                'httpsekonomirepublikacoidberitaekonomisyariah-ekonomi200106q3oben368-topur-marketplace-syariah-produk-lokal-dari-purwakarta',
                'news_url' => 'https://ekonomi.republika.co.id/berita/ekonomi/syariah-ekonomi/20/01/06/q3oben368-topur-marketplace-syariah-produk-lokal-dari-purwakarta?',
                'news_image' => 'img_news/01JVYQEJBZ34R1VZEV99SC25XP.jpg',
                'news_avatar' => 'img_news/01JVYPTN6JB6W8ZKASCC0WTMYV.jpg',
                'news_content' => '<p>Topur, Marketplace Syariah Produk Lokal dari Purwakarta</p>',
                'created_at' => '2025-05-23 20:49:13',
                'updated_at' => '2025-05-23 21:06:15'
            ],
            [
                'uuid' => 'f4d07ae5-8836-4c86-b424-f46e456d86f2',
                'news_fullname' => 'mppmadukara',
                'news_slug' => 'httpswwwinstagramcompc2g44m7ryj7',
                'news_url' => 'https://www.instagram.com/p/C2G44m7RYJ7/',
                'news_image' => 'img_news/01JVVJHVC69J1XS862KJVZNHTC.png',
                'news_avatar' => 'img_news/01JVVJHVPD8SNZ3V6SXA0PQNGW.jpg',
                'news_content' => '<p>14 Desember 2023 kemarin, telah diresmikan Pojok Wirausaha pada momen hari ulang tahun MPP Bale Madukara ke 3 tahun.</p>',
                'created_at' => '2025-05-22 15:42:57',
                'updated_at' => '2025-05-22 16:18:33',
            ],
            [
                'uuid' => 'bcc10e75-64e3-4046-ad24-a1dcbc942c4a',
                'news_fullname' => 'mppmadukara',
                'news_slug' => 'httpswwwinstagramcompdlyra7bz2ed',
                'news_url' => 'https://www.instagram.com/p/DLyrA7Bz2ED/',
                'news_image' => 'img_news/pw.jpg',
                'news_avatar' => 'img_news/01JVVHSJC3TPR2257RV2282VQ5.jpg',
                'news_content' => '<p>Pojok Wirausaha di MPP Bale Madukara merupakan fasilitas yang disediakan untuk mendukung para pelaku usaha khususnya di Kabupaten Purwakarta, dalam mengembangkan usahanya.<br>Sobat bisa memanfaatkan berbagai layanan yang tersedia.</p>',
                'created_at' => '2025-07-09 15:41:05',
                'updated_at' => '2025-07-10 06:58:45',
            ]
        ];

        foreach ($news as $data) {
            $model = new News($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
