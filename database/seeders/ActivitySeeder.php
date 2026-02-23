<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // Activity::factory(50)->create();
        $activities = [
            [
                'uuid' => '0027df98-0167-4148-92c8-78d564e4197d',
                'activity_title' => 'Brainstorming',
                'activity_slug' => 'brainstorming',
                'activity_description' => '<p><strong>Tujuan utama dari brainstorming</strong> adalah untuk <strong>menghasilkan sebanyak mungkin ide atau solusi kreatif dalam waktu singkat, tanpa adanya penghakiman atau kritik di awal.</strong> Ini adalah metode yang sangat efektif untuk memecahkan masalah, mengembangkan konsep baru, atau menemukan pendekatan inovatif.&nbsp;</p><p>Berikut adalah beberapa tujuan spesifik dari brainstorming:&nbsp;</p><ol><li><strong>Menghasilkan Kuantitas Ide:</strong></li></ol><p><br>Tujuan pertama dan paling mendasar adalah untuk mendorong peserta mengeluarkan ide sebanyak-banyaknya, bahkan ide yang tampaknya \"gila\" atau tidak mungkin. Kuantitas seringkali mengarah pada kualitas setelah proses penyaringan.<br>&nbsp;</p><ol><li><strong>Mendorong Kreativitas:</strong></li></ol><p><br>Brainstorming menciptakan lingkungan yang bebas dan non-judgmental, yang memicu peserta untuk berpikir di luar kebiasaan, merangsang pemikiran lateral, dan menemukan koneksi antar ide yang tidak terduga.<br>&nbsp;</p><ol><li><strong>Memecahkan Masalah Secara Kreatif/Inovatif:</strong></li></ol><p><br>Ketika menghadapi masalah yang kompleks atau buntu, brainstorming membantu mencari solusi yang tidak konvensional dan fresh, yang mungkin tidak terpikirkan jika hanya mengandalkan pemikiran individual atau struktural.<br>&nbsp;</p><ol><li><strong>Meningkatkan Kolaborasi dan Sinergi Tim:</strong></li></ol><p><br>Sesi brainstorming mendorong interaksi aktif antar anggota tim. Ide dari satu orang dapat memicu ide lain, menciptakan sinergi di mana \"keseluruhan lebih besar dari jumlah bagiannya.\" Ini juga membantu membangun pemahaman bersama dan rasa kepemilikan terhadap solusi.<br>&nbsp;</p><ol><li><strong>Membuka Perspektif Baru:</strong></li></ol><p><br>Dengan melibatkan individu dari berbagai latar belakang, pengalaman, dan keahlian, brainstorming memungkinkan masalah atau peluang dilihat dari berbagai sudut pandang yang berbeda, memperkaya solusi yang dihasilkan.<br>&nbsp;</p><ol><li><strong>Memperjelas atau Mewujudkan Ide Abstrak:</strong></li></ol><p><br>Ide-ide yang awalnya samar-samar atau terlalu luas dapat diperjelas dan didetailkan melalui proses brainstorming, menjadikannya lebih konkret dan bisa diimplementasikan.<br>&nbsp;</p><ol><li><strong>Meningkatkan Kualitas Pengambilan Keputusan:</strong></li></ol><p><br>Dengan banyaknya pilihan ide yang dihasilkan, tim memiliki dasar yang lebih luas untuk mengevaluasi dan memilih solusi terbaik yang paling sesuai dengan tujuan.<br>&nbsp;</p><ol><li><strong>Mengurangi Bias dan Prasangka:</strong></li></ol><p><br>Aturan dasar brainstorming yang melarang kritik di awal memastikan bahwa semua ide, terlepas dari siapa yang mengemukakannya atau seberapa \"aneh\" idenya, mendapatkan kesempatan untuk didengarkan. Ini meminimalkan bias dan mendorong partisipasi aktif dari semua anggota.<br>&nbsp;</p><ol><li><strong>Mengasah Kemampuan Individu:</strong></li></ol><p><br>Melalui proses brainstorming, individu melatih kemampuan berpikir kritis, mengemukakan pendapat, mendengarkan, dan membangun ide dari orang lain.<br>&nbsp;<br>Singkatnya, brainstorming adalah alat yang ampuh untuk <strong>memaksimalkan potensi kreatif kolektif suatu kelompok untuk menemukan solusi inovatif dan efektif terhadap tantangan.</strong><br> <br><a href=\"https://g.co/kgs/6SXx1Xn\"><strong style=\"text-decoration: underline;\">Villa Syariah Omira di Purwakarta</strong></a> tampaknya menjadi pilihan yang menarik untuk <strong>brainstorming dan aktivitas produktif</strong>. Selain menawarkan suasana yang nyaman, villa ini juga memiliki <strong>bisnis center</strong> yang bisa digunakan untuk diskusi lebih lanjut dan pengembangan ide.<br> <br>Jika Anda ingin mengoptimalkan sesi brainstorming di sana, beberapa hal yang bisa dipersiapkan:<br> <br>·&nbsp; &nbsp; &nbsp; &nbsp;<strong>Agenda brainstorming yang terstruktur, </strong>fokus pada inovasi bisnis atau strategi ekspansi.<br>&nbsp;<br>·&nbsp; &nbsp; &nbsp; &nbsp;<strong>Fasilitas pendukung</strong>, pastikan ruang bisnis center memiliki akses internet, alat presentasi, dan kenyamanan untuk diskusi panjang.<br> <br>·&nbsp; &nbsp; &nbsp; &nbsp;<strong>Dokumentasi hasil brainstorming,</strong> gunakan format mind map atau laporan untuk merangkum ide-ide utama.<br>&nbsp;</p>',
                'activity_image' => 'img_activities/01JWWNKVPPN7T1Z1RJ61NWQ97Q.jpg',
                'activity_category_uuid' => 'dbcf05bd-b74f-43fb-9a12-21edceaf3060',
                'activity_location' => 'Gn. Burangrang, Kampung Tegalsapi, Neglasari, Kec. Darangdan, Kabupaten Purwakarta, Jawa Barat 41163',
                'activity_date' => '2025-06-04 05:30:45',
                'created_at' => '2025-06-04 05:11:25',
                'updated_at' => '2025-06-04 05:30:45',
            ],
            [
                'uuid' => '0c16126c-dfc9-45a6-a496-22e25066dc63',
                'activity_title' => 'Lauching Aplikasi Yanliksmart V2.0 Purwakarta',
                'activity_slug' => 'lauching-aplikasi-yanliksmart-v20-purwakarta',
                'activity_description' => '<p>Kegiatan <strong>Sosialisasi Aplikasi Yanlik.Smart</strong> merupakan bagian dari upaya Pemerintah Kabupaten Purwakarta dalam memperkenalkan dan mendorong implementasi sistem digitalisasi pelayanan publik yang inovatif, terukur, dan berorientasi pada kepuasan masyarakat. Acara ini bertujuan untuk memberikan pemahaman menyeluruh kepada perangkat daerah, penyelenggara layanan publik, serta masyarakat umum mengenai manfaat, fitur, dan mekanisme penggunaan <strong>Aplikasi Yanlik.Smart</strong>.</p><p>Aplikasi <strong>Yanlik.Smart</strong> (Pelayanan Publik Sopan, Mudah, Amanah, Responsif, dan Tepat Waktu) adalah sistem yang dikembangkan untuk memfasilitasi pelaksanaan <strong>Survei Kepuasan Masyarakat (SKM)</strong> secara terintegrasi, sekaligus mendukung proses <strong>monitoring dan evaluasi pelayanan publik</strong> secara digital di lingkungan Kabupaten Purwakarta. Aplikasi ini direncanakan akan diimplementasikan penuh mulai tahun <strong>2024</strong>.</p><h3>🎯 <strong>Tujuan Kegiatan:</strong></h3><ul><li>Mensosialisasikan penggunaan dan manfaat Aplikasi Yanlik.Smart kepada stakeholder terkait.</li><li>Meningkatkan pemahaman tentang pentingnya pelaksanaan SKM dalam perbaikan pelayanan publik.</li><li>Memberikan pelatihan teknis dasar mengenai pengoperasian aplikasi.</li><li>Mendorong partisipasi aktif OPD dan unit layanan publik dalam pelaksanaan monitoring dan evaluasi kinerja berbasis data.</li></ul><h3>🗂️ <strong>Ruang Lingkup Acara:</strong></h3><ul><li>Pemaparan latar belakang dan urgensi pengembangan aplikasi.</li><li>Demo penggunaan aplikasi Yanlik.Smart.</li><li>Diskusi interaktif dan sesi tanya jawab.</li><li>Rencana tindak lanjut implementasi di tiap OPD.</li></ul><h3>👥 <strong>Peserta Kegiatan:</strong></h3><ul><li>Perwakilan Organisasi Perangkat Daerah (OPD) se-Kabupaten Purwakarta.</li><li>Petugas front office dan pengelola pelayanan publik.</li><li>Tim pengelola SKM dan evaluasi layanan.</li><li>Lembaga masyarakat/komunitas pemantau layanan publik.</li></ul><h3>🌟 <strong>Manfaat yang Diharapkan:</strong></h3><ul><li>Terciptanya pemahaman yang merata terkait penggunaan aplikasi di seluruh unit pelayanan publik.</li><li>Tersusunnya rencana kerja masing-masing OPD untuk mendukung implementasi aplikasi.</li><li>Terwujudnya komitmen bersama dalam membangun budaya pelayanan publik yang profesional, akuntabel, dan partisipatif berbasis teknologi.</li></ul>',
                'activity_image' => 'img_activities/01JVSP8FPNN9TGNHGX5PWFH3YZ.jpg',
                'activity_category_uuid' => 'dbcf05bd-b74f-43fb-9a12-21edceaf3060',
                'activity_location' => 'Purwakarta',
                'activity_date' => '2024-09-27 01:00:00',
                'created_at' => '2025-05-21 15:09:15',
                'updated_at' => '2025-05-24 09:50:09',
            ],
            [
                'uuid' => '42cb7088-1c93-49ff-b6de-040d1727a904',
                'activity_title' => 'Study Banding Penerapan Aplikasi PEKPPP di kabupaten Garut',
                'activity_slug' => 'study-banding-penerapan-aplikasi-pekppp-di-kabupaten-garut',
                'activity_description' => '<p>Kegiatan <strong>Study Banding Penerapan Aplikasi PEKPPP (Pemantauan dan Evaluasi Kinerja Penyelenggaraan Pemerintahan Daerah)</strong> di Kabupaten Garut merupakan inisiatif pembelajaran antar daerah yang bertujuan untuk mengetahui secara langsung penerapan terbaik (best practice) penggunaan aplikasi PEKPPP dalam mendukung efektivitas monitoring dan evaluasi kinerja penyelenggaraan pemerintahan.</p><p>Melalui kegiatan ini, peserta study banding akan mendapatkan wawasan tentang mekanisme pelaksanaan, integrasi data, serta strategi peningkatan kualitas layanan pemerintahan berbasis digital yang telah diterapkan oleh Pemerintah Kabupaten Garut. Kegiatan ini diharapkan menjadi referensi berharga dalam mengembangkan sistem serupa di daerah asal peserta, guna mendorong tata kelola pemerintahan yang lebih transparan, akuntabel, dan responsif.</p><h3>🎯 <strong>Tujuan Kegiatan:</strong></h3><ul><li>Mempelajari penerapan aplikasi PEKPPP di Kabupaten Garut sebagai model percontohan.</li><li>Mengidentifikasi faktor keberhasilan, tantangan, serta solusi yang diterapkan dalam implementasi aplikasi PEKPPP.</li><li>Membangun jejaring kerja sama antar pemerintah daerah dalam bidang pemantauan dan evaluasi kinerja pemerintahan.</li><li>Menyusun rencana tindak lanjut (RTL) untuk replikasi dan pengembangan sistem serupa di daerah peserta study banding.</li></ul><h3>🗂️ <strong>Ruang Lingkup Kegiatan:</strong></h3><ul><li>Paparan teknis oleh Tim Pengelola Aplikasi PEKPPP Kabupaten Garut.</li><li>Diskusi interaktif dan tanya jawab terkait kebijakan, infrastruktur, dan SDM pendukung aplikasi.</li><li>Kunjungan langsung ke unit kerja terkait yang menggunakan aplikasi PEKPPP.</li><li>Penandatanganan nota kesepahaman atau kerja sama (jika relevan).</li></ul><h3>👥 <strong>Peserta Kegiatan:</strong></h3><ul><li>Pejabat struktural dan fungsional dari instansi yang membidangi perencanaan, evaluasi kinerja, dan sistem informasi pemerintahan.</li><li>Tim teknis pengembang atau pengelola aplikasi e-Government.</li><li>Perwakilan dari pemerintah daerah peserta study banding.</li></ul><h3>🌟 <strong>Manfaat yang Diharapkan:</strong></h3><ul><li>Peningkatan pemahaman teknis dan kebijakan terkait sistem pemantauan kinerja berbasis aplikasi.</li><li>Adopsi praktik baik yang dapat diimplementasikan secara kontekstual di daerah masing-masing.</li><li>Dukungan dalam percepatan transformasi digital pemerintahan yang efektif dan efisien.</li></ul>',
                'activity_image' => 'img_activities/01JVSP2AWJXKKRX8S5VXJ3XM7B.jpg',
                'activity_category_uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_location' => 'Pemerintahan Garut',
                'activity_date' => '2024-06-27 02:00:00',
                'created_at' => '2025-05-21 15:05:53',
                'updated_at' => '2025-05-24 09:59:21',
            ],
            [
                'uuid' => '65cf3793-b77c-4a77-b023-f54a2be526bc',
                'activity_title' => 'Navigating Success in Business: Strategies for Entrepreneurship Triumph',
                'activity_slug' => 'navigating-success-in-business-strategies-for-entrepreneurship-triumph',
                'activity_description' => '<p>Seminar bisnis berjudul \"Navigating Success in Business: Strategies for Entrepreneurship Triumph\", yang diselenggarakan oleh Pusat Inkubator Bisnis STAI DR. KH. EZ Muttaqien Purwakarta.<br>Acara ini berlangsung pada Minggu, 26 November 2023, pukul 13.00 hingga selesai, di Auditorium STAI DR. KH. EZ Muttaqien Purwakarta. Seminar ini menghadirkan sejumlah pembicara berpengalaman di bidang kewirausahaan, termasuk:</p><ul><li>Dr. Surya Hadi Darma, M.Ud – Ketua STAI DR. KH. EZ Muttaqien Purwakarta</li><li>Hamdan Ardiansyah, M.Pd – Kepala Pusat Inkubator Bisnis STAI DR. KH. EZ Muttaqien Purwakarta</li><li>Ilham Mauluddin – Owner AMILY GROUP</li><li>H. Pipin Sopian, S.Sos, IMRI – Founder Purwakarta Leadership Center</li><li>Salman Al Farisi – Direktur CV NUPARIS</li></ul><p>Peserta seminar akan mendapatkan manfaat seperti:</p><ul><li>E-Certificate</li><li>Networking (Relasi)</li><li>Ilmu yang bermanfaat</li></ul><p>Acara ini terbuka untuk umum, sehingga siapa saja yang tertarik dengan strategi sukses dalam berbisnis bisa menghadiri dan berdiskusi langsung dengan para pembicara. Informasi lebih lanjut dapat diakses melalui<strong> </strong><a href=\"http://bit.ly/SeminarKWU_NavigatingSuccessBusiness\"><strong style=\"text-decoration: underline;\">LINK</strong></a><br>Bagaimana pengalaman Anda dalam acara ini? Apakah ada poin menarik dari diskusi yang ingin Anda bagikan?</p>',
                'activity_image' => 'img_activities/01JVYJQCCZWN4RPXC9FYHNS49X.jpg',
                'activity_category_uuid' => '3bb55ec0-b275-4b34-bcda-7414ff511d76',
                'activity_location' => 'usat Inkubator Bisnis STAI DR. KH. EZ Muttaqien Purwakarta.',
                'activity_date' => '2023-11-26 06:00:00',
                'created_at' => '2025-05-23 12:43:41',
                'updated_at' => '2025-05-24 09:40:20',
            ],
            [
                'uuid' => '6e51ab6d-fc63-4d54-9853-73ce4bdd799c',
                'activity_title' => 'Petugas Penilai Badan Karantina',
                'activity_slug' => 'petugas-penilai-badan-karantina',
                'activity_description' => '<p>Penilaian yang dilakukan oleh Petugas Penilai Badan Karantina Hewan, Ikan, dan Tumbuhan Jawa Barat berpedoman pada Peraturan Menteri Pertanian Nomor 38/Permentan/OT.140/3/2014 yang mengatur Pelaksanaan Tindakan Karantina Tumbuhan di Luar Tempat Pemasukan dan Pengeluaran.&nbsp;<br>Dalam praktiknya, peraturan ini memastikan bahwa tindakan karantina tetap dilakukan meskipun produk atau media pembawa tidak berada di titik masuk atau keluar resmi. Ini mencakup:</p><ul><li>Pemeriksaan dan sertifikasi terhadap produk pertanian yang beredar di dalam negeri.</li><li>Pengawasan terhadap organisme pengganggu tumbuhan untuk mencegah penyebaran hama dan penyakit.</li><li>Penilaian fasilitas seperti Rumah Pengemas, memastikan kepatuhan terhadap standar karantina.</li></ul><p>Tampak inspeksi atau evaluasi fasilitas yang sedang berlangsung, dengan petugas yang memeriksa dokumen dan berdiskusi mengenai kepatuhan terhadap regulasi. Apabila ada poin spesifik dari penilaian ini yang ingin di bahas lebih lanjut? Kami bisa membantu mencari detail prosedur atau persyaratan tambahan!</p>',
                'activity_image' => 'img_activities/01JVYBEMP6YR5YD2TZ3ETZ71D4.jpg',
                'activity_category_uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_location' => 'Desa Bunihayu, Kec. Jalancagak, Kabupaten Subang, Jawa Barat 41281',
                'activity_date' => '2025-01-10 10:36:00',
                'created_at' => '2025-05-23 10:36:34',
                'updated_at' => '2025-05-24 09:34:30',
            ],
            [
                'uuid' => '84feb91b-4b49-45f3-b221-ef78827e20b7',
                'activity_title' => 'Lauching Aplikasi Yanliksmart V1.0 Purwakarta',
                'activity_slug' => 'lauching-aplikasi-yanliksmart-v10-purwakarta',
                'activity_description' => '<p>Kegiatan <strong>Sosialisasi Aplikasi Yanlik.Smart</strong> merupakan bagian dari upaya Pemerintah Kabupaten Purwakarta dalam memperkenalkan dan mendorong implementasi sistem digitalisasi pelayanan publik yang inovatif, terukur, dan berorientasi pada kepuasan masyarakat. Acara ini bertujuan untuk memberikan pemahaman menyeluruh kepada perangkat daerah, penyelenggara layanan publik, serta masyarakat umum mengenai manfaat, fitur, dan mekanisme penggunaan <strong>Aplikasi Yanlik.Smart</strong>.</p><p>Aplikasi <strong>Yanlik.Smart</strong> (Pelayanan Publik Sopan, Mudah, Amanah, Responsif, dan Tepat Waktu) adalah sistem yang dikembangkan untuk memfasilitasi pelaksanaan <strong>Survei Kepuasan Masyarakat (SKM)</strong> secara terintegrasi, sekaligus mendukung proses <strong>monitoring dan evaluasi pelayanan publik</strong> secara digital di lingkungan Kabupaten Purwakarta. Aplikasi ini direncanakan akan diimplementasikan penuh mulai tahun <strong>2024</strong>.</p><h3>🎯 <strong>Tujuan Kegiatan:</strong></h3><ul><li>Mensosialisasikan penggunaan dan manfaat Aplikasi Yanlik.Smart kepada stakeholder terkait.</li><li>Meningkatkan pemahaman tentang pentingnya pelaksanaan SKM dalam perbaikan pelayanan publik.</li><li>Memberikan pelatihan teknis dasar mengenai pengoperasian aplikasi.</li><li>Mendorong partisipasi aktif OPD dan unit layanan publik dalam pelaksanaan monitoring dan evaluasi kinerja berbasis data.</li></ul><h3>🗂️ <strong>Ruang Lingkup Acara:</strong></h3><ul><li>Pemaparan latar belakang dan urgensi pengembangan aplikasi.</li><li>Demo penggunaan aplikasi Yanlik.Smart.</li><li>Diskusi interaktif dan sesi tanya jawab.</li><li>Rencana tindak lanjut implementasi di tiap OPD.</li></ul><h3>👥 <strong>Peserta Kegiatan:</strong></h3><ul><li>Perwakilan Organisasi Perangkat Daerah (OPD) se-Kabupaten Purwakarta.</li><li>Petugas front office dan pengelola pelayanan publik.</li><li>Tim pengelola SKM dan evaluasi layanan.</li><li>Lembaga masyarakat/komunitas pemantau layanan publik.</li></ul><h3>🌟 <strong>Manfaat yang Diharapkan:</strong></h3><ul><li>Terciptanya pemahaman yang merata terkait penggunaan aplikasi di seluruh unit pelayanan publik.</li><li>Tersusunnya rencana kerja masing-masing OPD untuk mendukung implementasi aplikasi.</li><li>Terwujudnya komitmen bersama dalam membangun budaya pelayanan publik yang profesional, akuntabel, dan partisipatif berbasis teknologi.</li></ul>',
                'activity_image' => 'img_activities/01JVSPCGS7HKRVPY8F9B50BNDJ.webp',
                'activity_category_uuid' => 'dbcf05bd-b74f-43fb-9a12-21edceaf3060',
                'activity_location' => 'Purwakarta',
                'activity_date' => '2023-12-15 01:00:00',
                'created_at' => '2025-05-21 15:11:27',
                'updated_at' => '2025-05-24 09:54:28',
            ],
            [
                'uuid' => '887a9e37-a248-4164-9710-6cc06609c022',
                'activity_title' => 'Balai Karantina Provinsi Jawa Barat',
                'activity_slug' => 'balai-karantina-provinsi-jawa-barats',
                'activity_description' => '<p>Berkonsultasi dengan <a href=\"https://www.instagram.com/karantinajawabarat/\"><strong style=\"text-decoration: underline;\">Balai Karantina Provinsi Jawa Barat</strong></a> mengenai pemenuhan persyaratan Rumah Pengemas. Itu langkah penting untuk memastikan kepatuhan terhadap regulasi pengemasan, terutama dalam sektor pertanian dan ekspor.</p><p>Balai Karantina menerbitkan berbagai sertifikat dan dokumen penting untuk memastikan kepatuhan terhadap regulasi karantina. Beberapa di antaranya meliputi:</p><ul><li>Sertifikat Karantina: Dokumen resmi yang menyatakan bahwa suatu produk telah melalui proses karantina dan bebas dari hama atau penyakit.</li><li>Surat Jalan Karantina: Dokumen yang diperlukan untuk transportasi hewan, ikan, atau tumbuhan yang telah lolos pemeriksaan karantina.</li><li>Health Certificate: Sertifikat kesehatan yang diperlukan untuk ekspor atau impor produk pertanian dan peternakan.</li><li>Phytosanitary Certificate: Dokumen yang memastikan bahwa produk tumbuhan memenuhi standar kesehatan dan bebas dari organisme pengganggu.</li><li>Dokumen Karantina Elektronik: Sistem digital yang digunakan untuk memproses dokumen karantina secara online, termasuk permohonan pemeriksaan karantina.</li></ul><p>Jika Anda sedang mengurus sertifikasi untuk Rumah Pengemas, sertifikat yang relevan mungkin termasuk Sertifikat Karantina Tumbuhan atau Health Certificate. Apakah ada dokumen tertentu yang sedang Anda cari? Kami bisa membantu menemukan prosedur pengurusannya!</p>',
                'activity_image' => 'img_activities/01JVYAGA5RT0Y43WJT9QRNAFBX.jpg',
                'activity_category_uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_location' => 'Jl. Soekarno Hatta No. 725 c, Jatisari, Bandung 40425',
                'activity_date' => '2024-11-10 10:20:00',
                'created_at' => '2025-05-23 10:20:01',
                'updated_at' => '2025-05-24 09:38:00',
            ],
            [
                'uuid' => 'd3232a5f-1f3a-4e3d-9a8e-66f014ec12eb',
                'activity_title' => 'OKKPD Provinsi Jawa Barat terkait pemenuhan persyaratan Permohonan PSAT (Penerapan Pangan yang Baik untuk Pangan Segar Asal Tumbuhan) dan Izin Rumah Pengemasan',
                'activity_slug' => 'okkpd-provinsi-jawa-barat-terkait-pemenuhan-persyaratan-permohonan-psat-penerapan-pangan-yang-baik-untuk-pangan-segar-asal-tumbuhan-dan-izin-rumah-pengemasan',
                'activity_description' => '<p>Konsultasi OKKPD Provinsi Jawa Barat terkait pemenuhan persyaratan Permohonan <strong>PSAT (Penerapan Pangan yang Baik untuk Pangan Segar Asal Tumbuhan)</strong> dan <strong>Izin Rumah Pengemasan.</strong> Ini merupakan langkah penting dalam memastikan keamanan pangan dan kepatuhan terhadap regulasi.</p><p>Dalam konteks ini, beberapa dokumen yang mungkin diperlukan meliputi:</p><ul><li><strong>Sertifikat Penerapan Penanganan yang Baik (SPPB PSAT)</strong> untuk memastikan produk pangan segar memenuhi standar keamanan.</li><li><strong>Izin Edar PSAT PD </strong>bagi pelaku usaha yang ingin mendistribusikan produk pangan segar.</li><li><strong>Registrasi PSAT PDUK</strong> untuk pencatatan produk pangan segar dalam sistem keamanan pangan nasional.</li><li><strong>Health Certificate</strong> sebagai jaminan bahwa produk pangan segar aman dikonsumsi.</li><li><strong>Izin Rumah Pengemasan</strong>, yang memastikan fasilitas pengemasan memenuhi standar kebersihan dan keamanan pangan.</li></ul><p>Apakah ada aspek tertentu dari proses ini yang ingin Anda bahas lebih lanjut? Kami bisa membantu menyusun dokumen atau mencari prosedur yang lebih spesifik!</p>',
                'activity_image' => 'img_activities/01JVYFAYQR5JHBW9HHWPVTVTAB.jpg',
                'activity_category_uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_location' => 'Jl. Raya Ir. H. Juanda No.358 Dago-Kota Bandung',
                'activity_date' => '2024-12-19 11:44:28',
                'created_at' => '2025-05-23 11:44:28',
                'updated_at' => '2025-05-24 10:02:50',
            ],
            [
                'uuid' => 'd702427b-c505-41e5-aed6-1a83a3e9b1f5',
                'activity_title' => 'Penilaian Penerapan Sanitasi Higiene',
                'activity_slug' => 'penilaian-penerapan-sanitasi-higiene',
                'activity_description' => '<p>Penilaian <strong>Penerapan Sanitasi Higiene</strong> oleh <strong>tim OKKPD Provinsi Jawa Barat</strong> merupakan bagian dari evaluasi keamanan pangan segar. Proses ini bertujuan untuk memastikan bahwa fasilitas pengolahan dan pengemasan pangan memenuhi standar kebersihan dan keamanan yang ditetapkan oleh <strong>Badan Pangan Nasional</strong>.&nbsp;<br><br>Dalam penilaian ini, beberapa aspek yang diperiksa meliputi:</p><ul><li><strong>Kelembagaan dan sistem pengawasan</strong> terhadap pangan segar.</li><li><strong>Penerapan standar kebersihan dan sanitasi</strong> di fasilitas pengemasan.</li><li><strong>Pendataan dan pembinaan kepada pelaku usaha</strong> terkait keamanan pangan.</li><li><strong>Sarana dan prasarana</strong> yang digunakan dalam proses produksi dan distribusi pangan segar</li></ul><p>Penilaian ini mengacu pada <strong>Peraturan Badan Pangan Nasional Nomor 12 Tahun 2023</strong>, yang menetapkan standar minimal dalam penyelenggaraan sistem manajemen pengawasan keamanan pangan segar. Hasil dari penilaian ini dapat menjadi dasar bagi OKKPD dalam memberikan sertifikasi atau rekomendasi kepada pelaku usaha yang ingin mendapatkan izin edar atau sertifikat keamanan pangan.<br><br>Apakah ada poin spesifik dari penilaian ini yang ingin Anda bahas lebih lanjut? Kami bisa membantu mencari prosedur atau persyaratan tambahan!</p>',
                'activity_image' => 'img_activities/01JVYFZ5GXYV8XC48DRBHYMR96.jpg',
                'activity_category_uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_location' => 'Ds. Bunihayu, Kec. Jalancagak, Kabupaten Subang, Jawa Barat 41281',
                'activity_date' => '2025-03-13 09:29:21',
                'created_at' => '2025-05-23 11:55:30',
                'updated_at' => '2025-05-24 09:31:37',
            ],
            [
                'uuid' => 'e7f0d3f1-81c7-42e6-a347-d8198b73a4f9',
                'activity_title' => 'Trade Expo Indonesia (TEI) 2023',
                'activity_slug' => 'trade-expo-indonesia-tei-2023',
                'activity_description' => '<p><strong>Trade Expo Indonesia (TEI) 2023</strong> adalah pameran dagang internasional terbesar di Indonesia yang diselenggarakan secara <strong>hibrida</strong> oleh <strong>Kementerian Perdagangan RI</strong>. Acara ini bertujuan untuk memperluas peluang ekspor dan mempertemukan pelaku usaha dengan pembeli dari berbagai negara.&nbsp;</p><h3>📌 <strong>Detail Acara</strong></h3><ul><li><strong>Pameran Luring</strong>:<br>&nbsp;📅 <strong>18–22 Oktober 2023</strong><br> 📍 <strong>Indonesia Convention Exhibition (ICE) BSD City, Tangerang</strong></li><li><strong>Pameran Daring</strong>:<br> 📅 <strong>18 Oktober—18 Desember 2023</strong><br> 🌐 <a href=\"https://tradexpoindonesia.com/\"><strong>tradexpoindonesia.com</strong></a></li></ul><p>🔹 <strong>Fokus dan Kegiatan</strong></p><p>TEI 2023 menghadirkan berbagai sektor industri, termasuk:</p><ul><li><strong>Produk ekspor unggulan Indonesia</strong> seperti makanan dan minuman, tekstil, furnitur, dan produk kreatif.</li><li><strong>Business Matching</strong> untuk mempertemukan eksportir dengan pembeli potensial.</li><li><strong>Seminar dan diskusi bisnis</strong> dengan pakar industri dan pemerintah.</li><li><strong>Pameran inovasi</strong> yang menampilkan produk-produk terbaru dari UMKM dan perusahaan besar.</li></ul><p>Apakah Anda terlibat dalam kegiatan ini atau ada aspek tertentu yang ingin Anda eksplorasi lebih lanjut? Kami bisa membantu mencari informasi tambahan!</p>',
                'activity_image' => 'img_activities/01JVYK71512MQ4SEFM8Y192Y2X.jpg',
                'activity_category_uuid' => '2628fcbc-e8a9-4eb0-9c0f-ea5f26d92f6a',
                'activity_location' => 'Indonesia Convention Exhibition (ICE) BSD City, Tangerang',
                'activity_date' => '2023-10-18 03:00:00',
                'created_at' => '2025-05-23 12:52:14',
                'updated_at' => '2025-05-24 09:43:46',
            ],
            [
                'uuid' => 'ffc12dd2-92a6-4812-a3f8-369c8d184434',
                'activity_title' => 'Pengembangan pemasaran produk UMKM di desa Pondok Bungur',
                'activity_slug' => 'pengembangan-pemasaran-produk-umkm-di-desa-pondok-bungur',
                'activity_description' => '<p>Seminar \"Strategi Pengembangan Produk untuk UMKM\" merupakan kegiatan yang bertujuan untuk mendorong pelaku usaha mikro, kecil, dan menengah agar mampu menciptakan produk yang lebih inovatif, berkualitas, dan siap bersaing di pasar. Melalui seminar ini, peserta akan mendapatkan pemahaman praktis mengenai cara mengembangkan produk mulai dari ide, desain, kemasan, hingga strategi pemasaran yang efektif.</p><p>Acara ini menjadi wadah belajar sekaligus berbagi pengalaman antar pelaku UMKM agar dapat terus bertumbuh dan beradaptasi dengan perkembangan pasar yang dinamis, terutama di era digital saat ini.</p><h3>🎯 <strong>Tujuan Kegiatan:</strong></h3><ul><li>Meningkatkan pemahaman pelaku UMKM tentang pengembangan produk yang berorientasi pasar.</li><li>Memberikan panduan praktis untuk meningkatkan kualitas dan tampilan produk.</li><li>Membantu UMKM membangun daya saing melalui inovasi dan kreativitas.</li></ul><h3>🗂️ <strong>Materi yang Akan Disampaikan:</strong></h3><ul><li>Menentukan keunikan produk (Unique Selling Point).</li><li>Inovasi desain dan pengemasan yang menarik.</li><li>Pentingnya branding dan identitas produk.</li><li>Strategi pemasaran berbasis digital.</li><li>Studi kasus UMKM sukses mengembangkan produknya.</li></ul><h3>👤 <strong>Siapa yang Harus Hadir:</strong></h3><ul><li>Pemilik UMKM dari berbagai sektor usaha.</li><li>Wirausaha pemula yang ingin membangun produk unggulan.</li><li>Komunitas atau organisasi pembina UMKM.</li></ul><h3>💡 <strong>Manfaat yang Didapat Peserta:</strong></h3><ul><li>Wawasan baru tentang cara meningkatkan produk.</li><li>Inspirasi dari pelaku usaha sukses.</li><li>Relasi dan jaringan bisnis baru.</li><li>Sertifikat sebagai bentuk partisipasi dan apresiasi.</li></ul>',
                'activity_image' => 'img_activities/01JVSNVZK41830EQEWEZS30W9A.jpg',
                'activity_category_uuid' => '3bb55ec0-b275-4b34-bcda-7414ff511d76',
                'activity_location' => 'Pondok Bungur Purwakarta',
                'activity_date' => '2024-08-11 00:00:00',
                'created_at' => '2025-05-21 15:02:25',
                'updated_at' => '2025-05-24 09:56:57',
            ],
        ];

        foreach ($activities as $data) {
            $model = new Activity($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
