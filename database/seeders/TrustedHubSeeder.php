<?php

namespace Database\Seeders;

use App\Models\TrustedHub;
use Illuminate\Database\Seeder;

class TrustedHubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trustedhubs = [
            [
                'trustedhub_url' => 'https://oss.go.id/id',
                'trustedhub_description' => 'Online Single Submission (OSS) — sistem perizinan usaha terintegrasi dari BKPM. Urus Nomor Induk Berusaha (NIB) dan semua izin usaha dalam satu platform elektronik tanpa perlu ke kantor.'
            ],
            [
                'trustedhub_url' => 'https://pajak.go.id',
                'trustedhub_description' => 'Portal resmi DJP Kemenkeu — lapor SPT tahunan, bayar pajak online, daftar NPWP elektronik, dan akses seluruh layanan perpajakan untuk wajib pajak pribadi maupun badan usaha.'
            ],
            [
                'trustedhub_url' => 'https://siap.beacukai.go.id',
                'trustedhub_description' => 'SIAP Bea Cukai — layanan kepabeanan dan cukai elektronik Ditjen Bea Cukai. Urus dokumen impor, ekspor, pemberitahuan pabean, dan fasilitas kawasan berikat secara terintegrasi.'
            ],
            [
                'trustedhub_url' => 'https://ereg.pajak.go.id',
                'trustedhub_description' => 'e-Registration DJP — daftar NPWP secara online, perubahan data wajib pajak, dan pencabutan NPWP tanpa harus datang ke Kantor Pelayanan Pajak (KPP) terdekat.'
            ],
            [
                'trustedhub_url' => 'https://efiling.pajak.go.id',
                'trustedhub_description' => 'e-Filing DJP — lapor SPT Tahunan PPh Orang Pribadi dan Badan secara elektronik. Proses cepat, hemat waktu, dan bukti penerimaan elektronik (BPE) langsung dikirim ke email.'
            ],
            [
                'trustedhub_url' => 'https://bkpm.go.id',
                'trustedhub_description' => 'BKPM (Kementerian Investasi) — informasi peluang investasi, kemudahan berusaha, pelayanan terpadu satu pintu (PTSP) pusat, dan layanan konsultasi bagi investor domestik maupun asing.'
            ],
            [
                'trustedhub_url' => 'https://cctv.purwakartakab.go.id/',
                'trustedhub_description' => 'Papais CCTV Purwakarta — platform inovatif Diskominfo Kab. Purwakarta untuk memantau kondisi lalu lintas, keamanan wilayah, dan cuaca secara real-time langsung dari smartphone.'
            ],
            [
                'trustedhub_url' => 'https://cctv.jakarta.go.id',
                'trustedhub_description' => 'CCTV Jakarta Pemprov DKI — ratusan kamera pemantau lalu lintas dan keamanan di titik strategis seluruh Jakarta. Pantau kondisi jalan, persimpangan, dan kawasan publik secara langsung.'
            ],
            [
                'trustedhub_url' => 'https://ntmc.polri.go.id',
                'trustedhub_description' => 'National Traffic Management Centre (NTMC) Polri — info kondisi lalu lintas nasional, pemantauan jalan tol, laporan kecelakaan, dan panduan rute aman di seluruh wilayah Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://korlantas.polri.go.id',
                'trustedhub_description' => 'Korlantas Polri — perpanjangan SIM online, cek status tilang elektronik (ETLE), registrasi kendaraan, dan seluruh layanan kepolisian lalu lintas secara digital tanpa antre.'
            ],
            ['trustedhub_url' => 'https://etle-pmj.info', 'trustedhub_description' => 'E-TLE (Electronic Traffic Law Enforcement) — cek pelanggaran tilang kamera elektronik, konfirmasi, dan bayar denda tilang kendaraan secara online sesuai wilayah hukum Polda.'],
            [
                'trustedhub_url' => 'https://sim.korlantas.polri.go.id',
                'trustedhub_description' => 'SIM Online Korlantas — perpanjang Surat Izin Mengemudi (SIM A, B, C) secara online, pilih jadwal dan lokasi ujian, bayar PNBP, tanpa harus datang langsung di hari pertama.'
            ],
            [
                'trustedhub_url' => 'https://bpjs-kesehatan.go.id',
                'trustedhub_description' => 'BPJS Kesehatan — portal resmi JKN-KIS. Cek status kepesertaan, bayar iuran bulanan, daftar peserta baru, cari fasilitas kesehatan rekanan, dan unduh kartu JKN digital.'
            ],
            [
                'trustedhub_url' => 'https://pcare.bpjs-kesehatan.go.id',
                'trustedhub_description' => 'PCare BPJS Kesehatan — daftar antrian online di Puskesmas dan Fasilitas Kesehatan Tingkat Pertama (FKTP) terdekat. Pilih jam kunjungan dan dokter tanpa harus antre pagi-pagi.'
            ],
            [
                'trustedhub_url' => 'https://yankes.kemkes.go.id',
                'trustedhub_description' => 'Yankes Kemenkes RI — cari faskes (RS, Puskesmas, klinik) di seluruh Indonesia, cek akreditasi, layanan telemedicine, dan informasi program Jaminan Kesehatan Nasional.'
            ],
            [
                'trustedhub_url' => 'https://kemkes.go.id',
                'trustedhub_description' => 'Kementerian Kesehatan RI — portal kebijakan kesehatan nasional, data epidemiologi penyakit, program imunisasi, regulasi tenaga kesehatan, dan berita kesehatan terkini Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://sehatnegeriku.kemkes.go.id',
                'trustedhub_description' => 'Sehat Negeriku — media informasi dan edukasi kesehatan resmi Kemenkes RI. Berisi artikel kesehatan, tips hidup sehat, waspada penyakit musiman, dan program promosi kesehatan.'
            ],
            [
                'trustedhub_url' => 'https://farmalkes.kemkes.go.id',
                'trustedhub_description' => 'Farmalkes Kemenkes — informasi ketersediaan obat esensial, alat kesehatan, e-katalog obat, regulasi izin edar, dan pengawasan mutu farmasi dan alat kesehatan di Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://kemdikbud.go.id',
                'trustedhub_description' => 'Kemendikbudristek RI — portal resmi kebijakan pendidikan nasional, kurikulum Merdeka Belajar, program prioritas, peraturan, dan seluruh layanan pendidikan dari PAUD hingga perguruan tinggi.'
            ],
            [
                'trustedhub_url' => 'https://beasiswa.kemdikbud.go.id',
                'trustedhub_description' => 'Beasiswa Kemendikbudristek — pendaftaran KIP Kuliah, beasiswa unggulan, LPDP, dan berbagai program beasiswa pemerintah untuk pelajar, mahasiswa, dan peneliti Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://snpmb.bppp.kemdikbud.go.id',
                'trustedhub_description' => 'SNPMB BPPP — sistem nasional penerimaan mahasiswa baru PTN. Daftar dan ikuti seleksi SNBP (prestasi) dan SNBT (tes) untuk masuk perguruan tinggi negeri seluruh Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://pusatinformasi.kampusmerdeka.kemdikbud.go.id',
                'trustedhub_description' => 'Kampus Merdeka — program MBKM: magang bersertifikat, pertukaran mahasiswa, studi independen, riset, wirausaha, dan proyek kemanusiaan untuk mahasiswa aktif PTN dan PTS.'
            ],
            [
                'trustedhub_url' => 'https://gtk.kemdikbud.go.id',
                'trustedhub_description' => 'GTK Kemendikbud — layanan sertifikasi pendidik (PPG), pencairan tunjangan profesi guru, pengembangan kompetensi, dan informasi karir bagi guru dan tenaga kependidikan Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://dapodik.kemdikbud.go.id',
                'trustedhub_description' => 'Dapodik — database pokok pendidikan nasional yang mencakup data sekolah, siswa, guru, sarana prasarana, dan kurikulum di seluruh satuan pendidikan Indonesia secara real-time.'
            ],
            [
                'trustedhub_url' => 'https://dukcapil.kemendagri.go.id',
                'trustedhub_description' => 'Dukcapil Kemendagri — layanan administrasi kependudukan digital nasional. Urus e-KTP, akta kelahiran, kartu keluarga, dan dokumen sipil lainnya secara online bagi seluruh WNI.'
            ],
            [
                'trustedhub_url' => 'https://layananonline.dukcapil.kemendagri.go.id',
                'trustedhub_description' => 'Layanan Online Dukcapil — ajukan permohonan akta kelahiran, akta perkawinan, akta perceraian, akta kematian, dan perubahan KK tanpa harus datang ke kantor Dukcapil.'
            ],
            [
                'trustedhub_url' => 'https://disdukcapil.jakarta.go.id',
                'trustedhub_description' => 'Dukcapil DKI Jakarta — portal administrasi kependudukan khusus warga DKI. Urus e-KTP, KK, akta, dan pindah domisili secara online dengan antrian digital tanpa antre panjang.'
            ],
            [
                'trustedhub_url' => 'https://pedulilindungi.id',
                'trustedhub_description' => 'PeduliLindungi — platform digital kesehatan dan identitas vaksinasi resmi pemerintah. Cek sertifikat vaksin COVID-19, status booster, dan riwayat vaksinasi untuk keperluan perjalanan.'
            ],
            [
                'trustedhub_url' => 'https://identitaskita.kemendagri.go.id',
                'trustedhub_description' => 'Identitas Kita — layanan verifikasi dan autentikasi identitas kependudukan digital berbasis NIK dari Kemendagri, digunakan untuk integrasi layanan publik dan perbankan digital.'
            ],
            [
                'trustedhub_url' => 'https://adminduk.jakarta.go.id',
                'trustedhub_description' => 'Adminduk Jakarta — sistem administrasi kependudukan terintegrasi milik Pemprov DKI Jakarta. Daftar online, upload dokumen, dan pantau status permohonan dari rumah.'
            ],
            [
                'trustedhub_url' => 'https://kai.id',
                'trustedhub_description' => 'KAI (Kereta Api Indonesia) — beli tiket kereta api jarak jauh, KRL Commuter Line, LRT Jabodebek, dan Whoosh (kereta cepat). Cek jadwal, pilih kursi, dan bayar langsung online.'
            ],
            [
                'trustedhub_url' => 'https://samsat-pkb.jakarta.go.id',
                'trustedhub_description' => 'SAMSAT DKI Jakarta — cek besaran dan bayar Pajak Kendaraan Bermotor (PKB) online, pengesahan STNK tahunan, perpanjangan STNK 5 tahunan, dan mutasi kendaraan warga Jakarta.'
            ],
            [
                'trustedhub_url' => 'https://hubdat.dephub.go.id',
                'trustedhub_description' => 'Ditjen Hubungan Darat Kemenhub RI — izin trayek angkutan umum, uji berkala kendaraan bermotor, regulasi transportasi darat, dan kebijakan LLAJ nasional seluruh Indonesia.'
            ],
            [
                'trustedhub_url' => 'https://elsamsat.go.id',
                'trustedhub_description' => 'e-SAMSAT Nasional — platform pembayaran pajak kendaraan bermotor online yang bisa digunakan lintas daerah/provinsi. Bayar PKB tanpa harus ke kantor SAMSAT setempat.'
            ],
            [
                'trustedhub_url' => 'https://bpsdm.dephub.go.id',
                'trustedhub_description' => 'BPSDM Kemenhub — Badan Pengembangan SDM Perhubungan. Informasi pelatihan, sertifikasi kompetensi, dan diklat untuk tenaga transportasi darat, laut, udara, dan perkeretaapian.'
            ],
            [
                'trustedhub_url' => 'https://tiket.damri.co.id',
                'trustedhub_description' => 'DAMRI Online — pesan tiket bus DAMRI untuk rute bandara, antarkota, dan trans-provinsi. Tersedia armada reguler dan premium dengan pemesanan mudah lewat website resmi.'
            ],
            [
                'trustedhub_url' => 'https://lpse.lkpp.go.id',
                'trustedhub_description' => 'LPSE LKPP — Layanan Pengadaan Secara Elektronik dari LKPP. Ikuti tender, pengadaan barang/jasa pemerintah, e-purchasing, dan e-katalog secara transparan dan akuntabel.'
            ],
            [
                'trustedhub_url' => 'https://data.go.id',
                'trustedhub_description' => 'Portal Satu Data Indonesia — ribuan dataset terbuka dari kementerian, lembaga, dan pemda. Unduh data statistik, geospasial, ekonomi, sosial untuk riset dan pengembangan aplikasi.'
            ],
            [
                'trustedhub_url' => 'https://lapor.go.id',
                'trustedhub_description' => 'SP4N-LAPOR! — sistem pengaduan layanan publik nasional terintegrasi. Laporkan masalah layanan pemerintah, pantau tindak lanjut, dan beri penilaian kepuasan secara transparan.'
            ],
            [
                'trustedhub_url' => 'https://satu.go.id',
                'trustedhub_description' => 'Satu Data Indonesia — portal integrasi dan berbagi data antara pemerintah pusat dan daerah. Standarisasi metadata, interoperabilitas data, dan keterbukaan informasi publik.'
            ],
            [
                'trustedhub_url' => 'https://jdih.kemenkumham.go.id',
                'trustedhub_description' => 'JDIH Kemenkumham — jaringan dokumentasi dan informasi hukum nasional. Akses undang-undang, PP, Perpres, Permen, dan putusan pengadilan seluruh Indonesia secara gratis.'
            ],
            [
                'trustedhub_url' => 'https://spse.pu.go.id',
                'trustedhub_description' => 'SPSE Kemen PUPR — sistem pengadaan elektronik khusus proyek konstruksi dan infrastruktur Kementerian PUPR. Tender jalan, jembatan, bendungan, dan gedung pemerintah secara online.'
            ],
        ];

        foreach ($trustedhubs as $trustedhub) {
            TrustedHub::create($trustedhub);
        }
    }
}
