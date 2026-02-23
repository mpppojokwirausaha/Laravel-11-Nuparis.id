<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Event::factory(12)->create();
        $event = [
            [
                'uuid' => '6c640d88-1339-46c7-9ba8-bc7d175210d2',
                'event_title' => 'Promo Event Juni - Oktober 2025',
                'event_slug' => 'promo-event-juni-oktober-2025',
                'event_description' => '<p>Hai hai hai tukang jajan Udah tau belum? Kalo kalian jajan di Pizza Slice Bisa dapet banyak hadiah loh Caranya easy banget Kalian tuh tinggal ikutin event lomba video kreatif ini nih Dan pendaftarannya pun free Gratis loh temen temen Cukup beli produk Pizza Slice yang ini nih Kalian udah bisa langsung ikutan eventnya loh Ini mah udah pasti gak bakal rugi deh Jajan enak, perut kenyang Terus disegerin sama nogu Nah tapi jangan lupa Semuanya pas sambil makan di videoin Semenarik mungkin ya Terus kalian share deh temen temen Berhubung ada 3 brand Di event ini otomatis hadiahnya tuh bakal banyak banget temen temen Total hadiah uang tunai nya aja hampir ratusan Dan juga dapet kupon voucher cashback Untuk pembelian sepeda Listrik YADEA ini nih, Satu lagi kalo beruntung Di kemasan nogu nya ada hadiah langsung ya temen temen Sampai ratusan Beneran deh gak pake ribet Tinggal jajan, video, upload Dapet hadiah, seru gak? Oh iya, untuk mendaftaran event Cuma bisa di cabang Pizza Slice Sadang ya temen temen Untuk informasi lebih lanjut Klik nomer ini ya Ayo ikutan 0819 9339 0000<br><br><em style=\"text-decoration: underline;\">Video&nbsp;</em><a href=\"https://www.tiktok.com/@ditaadiniputri12/video/7509684245179223314?is_from_webapp=1&amp;sender_device=pc&amp;web_id=7500220589659932161\"><em style=\"text-decoration: underline;\">Tiktok!</em></a></p>',
                'event_image' => 'img_events/01JWRMBHTTZQWPC55PEAA5YY9T.jpg',
                'event_date_start' => '2025-07-01 00:15:24',
                'event_date_end' => '2025-09-30 00:15:24',
                'event_location' => 'Outlet Pizza Slice Cabang Sadang - Purwakarta',
                'event_category_uuid' => '0c28d67d-b709-4c61-be3f-34f75684d83b',
                'event_price' => '70000',
                'created_at' => '2025-06-02 15:32:26',
                'updated_at' => '2025-07-02 07:43:59',
            ],
            [
                'uuid' => 'ad98768d-d211-4cd1-8fb9-576e9374fb1f',
                'event_title' => 'Sosialisasi Kewajiban  Perpajakan bagi pelaku UMKM',
                'event_slug' => 'sosialisasi-kewajiban-perpajakan-bagi-pelaku-umkm',
                'event_description' => '<p>Seminar <strong>Sosialisasi Kewajiban Perpajakan bagi Pelaku UMKM</strong> merupakan kegiatan edukatif yang bertujuan untuk meningkatkan pemahaman pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) terhadap peraturan perpajakan yang berlaku di Indonesia. Dalam kegiatan ini, peserta akan diberikan informasi terkait kewajiban pajak yang harus dipenuhi oleh pelaku usaha, termasuk cara pelaporan, pembayaran, serta manfaat kepatuhan terhadap pajak bagi keberlanjutan usaha.</p><p><br></p><p>Dengan menghadirkan narasumber dari instansi perpajakan dan praktisi yang berpengalaman, seminar ini diharapkan dapat mendorong terciptanya ekosistem usaha yang lebih tertib administrasi dan taat pajak, sehingga UMKM dapat berkembang secara sehat dan legal di mata hukum.</p><p><br></p><p>🎯 <strong>Tujuan Kegiatan:</strong></p><ul><li>Meningkatkan kesadaran dan pengetahuan pelaku UMKM tentang pentingnya membayar pajak.</li><li>Memberikan pemahaman terkait jenis-jenis pajak yang relevan bagi UMKM (seperti PPh Final UMKM, PPN, dsb).</li><li>Memandu peserta dalam proses pendaftaran NPWP, pelaporan SPT, dan penggunaan aplikasi perpajakan digital (e-Filing, e-Bupot, dsb).</li><li>Menjawab berbagai kendala dan pertanyaan praktis terkait pajak dalam kegiatan usaha sehari-hari.</li></ul><p><br></p><p>📚 <strong>Materi yang Dibahas:</strong></p><ul><li>Dasar hukum dan kebijakan perpajakan untuk UMKM.</li><li>Jenis dan tarif pajak yang berlaku bagi pelaku usaha kecil.</li><li>Prosedur administrasi pajak: NPWP, SPT, faktur pajak, bukti potong.</li><li>Penggunaan layanan DJP Online dan aplikasi perpajakan digital.</li><li>Insentif pajak dan kemudahan yang tersedia bagi UMKM.</li></ul><p><br></p><p>👤 <strong>Sasaran Peserta:</strong></p><ul><li>Pelaku UMKM dari berbagai sektor usaha.</li><li>Wirausahawan pemula yang baru menjalankan usahanya.</li><li>Pendamping atau konsultan UMKM di daerah.</li></ul><p><br></p><p>💡 <strong>Manfaat bagi Peserta:</strong></p><ul><li>Memahami hak dan kewajiban perpajakan sebagai pelaku usaha.</li><li>Terhindar dari sanksi akibat ketidaktahuan pajak.</li><li>Menjadi pelaku usaha yang patuh pajak dan memiliki legalitas kuat.</li><li>Meningkatkan kepercayaan mitra dan konsumen terhadap bisnis yang dijalankan.</li></ul>',
                'event_image' => 'img_events/01JVSPSERAEG9H0G2NPHP1ZPT7.jpg',
                'event_date_start' => '2025-05-22 11:20:34',
                'event_date_end' => '2025-05-22 11:20:34',
                'event_location' => 'Bale Madukara Kabupaten Purwakarta - Jl. Jendral Sudirman No.Kel, Nagri Kaler, Kec. Purwakarta, Kabupaten Purwakarta, Jawa Barat 41115',
                'event_category_uuid' => '659fc929-9cff-4c8e-91e3-7058978e28e9',
                'event_price' => '0',
                'created_at' => '2025-05-21 15:18:31',
                'updated_at' => '2025-05-22 11:20:34',
            ],
            [
                'uuid' => 'd877bac4-a0eb-477a-aabe-81073569a373',
                'event_title' => 'Kualitas Penampilan dan sikap profesional With Rhana Cahya Nugraha',
                'event_slug' => 'kualitas-penampilan-dan-sikap-profesional-with-rhana-cahya-nugraha',
                'event_description' => '<p>Meningkatkan kualitas penampilan dan sikap profesional adalah proses pengembangan diri yang berkelanjutan untuk memperluas pengetahuan, mengasah kemampuan teknis maupun soft skill, serta membentuk sikap kerja yang bertanggung jawab, etis, dan berorientasi pada kualitas. Hal ini penting dilakukan untuk menghadapi tantangan dunia kerja yang semakin kompetitif dan dinamis.</p><h3>🎯 <strong>Tujuan Utama:</strong></h3><ul><li>Mengembangkan kompetensi sesuai bidang kerja.</li><li>Meningkatkan efektivitas dan efisiensi dalam menjalankan tugas.</li><li>Membangun etos kerja, disiplin, dan tanggung jawab profesional.</li><li>Meningkatkan daya saing individu di dunia kerja.</li></ul><h3>🛠️ <strong>Contoh Keterampilan yang Ditingkatkan:</strong></h3><ul><li><strong>Keterampilan teknis:</strong> seperti analisis data, desain grafis, pemrograman.</li><li><strong>Keterampilan komunikasi:</strong> presentasi, negosiasi, public speaking.</li><li><strong>Keterampilan manajerial:</strong> kepemimpinan, pengambilan keputusan, manajemen waktu.</li><li><strong>Keterampilan interpersonal:</strong> kerja tim, empati, dan kecerdasan emosional.</li></ul><h3>📈 <strong>Manfaat:</strong></h3><ul><li>Meningkatkan kinerja dan produktivitas kerja.</li><li>Memperluas peluang karier dan jenjang jabatan.</li><li>Menjadi pribadi yang adaptif dan siap menghadapi perubahan.</li><li>Menumbuhkan rasa percaya diri dan profesionalitas dalam bekerja.</li></ul>',
                'event_image' => 'img_events/01JVRYY6M89HSP9G2JJ7VM12BM.jpg',
                'event_date_start' => '2024-05-16 08:00:00',
                'event_date_end' => '2024-05-16 08:00:00',
                'event_location' => 'Video Conference',
                'event_category_uuid' => '14d8af7d-89be-4a44-b61e-811389d18326',
                'event_price' => '0',
                'created_at' => '2025-05-21 08:21:41',
                'updated_at' => '2025-05-24 10:24:09',
            ],
            [
                'uuid' => 'ef7d2b88-1c99-4d08-a33c-e41f98369b4c',
                'event_title' => 'Peningkatan Kapasitas Wirausaha Industri Olahan Makanan dan Minuman KBLI 10 dan 11',
                'event_slug' => 'peningkatan-kapasitas-wirausaha-industri-olahan-makanan-dan-minuman-kbli-10-dan-11',
                'event_description' => '<p>Kegiatan <strong>Peningkatan Kapasitas Wirausaha Industri Olahan Makanan dan Minuman KBLI 10 dan 11</strong> merupakan program pembinaan yang ditujukan bagi pelaku usaha yang bergerak di sektor industri pengolahan makanan (KBLI 10) dan minuman (KBLI 11). Program ini bertujuan untuk memperkuat kompetensi wirausaha dalam mengelola usahanya secara lebih profesional, inovatif, dan berdaya saing tinggi.</p><p>Melalui kegiatan ini, peserta dibekali dengan pengetahuan dan keterampilan praktis yang mencakup proses produksi, keamanan pangan, manajemen usaha, legalitas produk, hingga strategi pemasaran berbasis digital. Harapannya, pelaku usaha dapat meningkatkan mutu produk, memperluas pasar, dan berkontribusi terhadap pertumbuhan ekonomi daerah.</p><h3>🎯 <strong>Tujuan Kegiatan:</strong></h3><ul><li>Meningkatkan kapasitas teknis dan manajerial pelaku industri makanan dan minuman.</li><li>Mendorong standarisasi dan keamanan produk sesuai regulasi (PIRT, BPOM, Halal, dll).</li><li>Memperkuat daya saing usaha melalui inovasi produk dan kemasan.</li><li>Membantu pelaku usaha memahami pentingnya pencatatan keuangan dan strategi pemasaran.</li></ul><h3>🗂️ <strong>Ruang Lingkup Materi:</strong></h3><ul><li>Standar produksi dan keamanan pangan.</li><li>Inovasi pengembangan produk dan diversifikasi rasa.</li><li>Desain kemasan yang menarik dan fungsional.</li><li>Legalitas dan perizinan usaha (NIB, PIRT, BPOM, Sertifikasi Halal).</li><li>Pengelolaan keuangan usaha mikro dan strategi pemasaran digital.</li></ul><h3>👥 <strong>Sasaran Peserta:</strong></h3><ul><li>Pelaku usaha mikro dan kecil di sektor makanan dan minuman.</li><li>Wirausaha baru yang ingin mengembangkan produk olahan pangan.</li><li>UMKM yang memiliki potensi untuk naik kelas dan menjangkau pasar lebih luas.</li></ul><h3>🌟 <strong>Manfaat bagi Peserta:</strong></h3><ul><li>Meningkatnya kualitas dan kelayakan produk untuk pasar modern.</li><li>Pengetahuan tentang regulasi dan izin usaha yang relevan.</li><li>Jaringan antar pelaku usaha dan peluang kemitraan.</li><li>Sertifikat pelatihan sebagai pengakuan kompetensi.</li></ul>',
                'event_image' => 'img_events/01JVS2DWGF3HBJVK8M6PG6HPP5.jpg',
                'event_date_start' => '2024-05-21 01:00:00',
                'event_date_end' => '2024-05-21 01:00:00',
                'event_location' => 'Bale Madukara Kabupaten Purwakarta - Jl. Jendral Sudirman No.Kel, Nagri Kaler, Kec. Purwakarta, Kabupaten Purwakarta, Jawa Barat 41115',
                'event_category_uuid' => '659fc929-9cff-4c8e-91e3-7058978e28e9',
                'event_price' => '0',
                'created_at' => '2025-05-21 09:21:25',
                'updated_at' => '2025-05-24 10:14:36',
            ],
        ];

        foreach ($event as $data) {
            $model = new Event($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
