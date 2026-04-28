<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>NUPARIS | Support Your Company Goals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-white">
    <section
        class="flex flex-col items-center justify-center min-h-screen sm:px-8 md:px-16 text-gray-900 -mt-16 sm:-mt-20 md:-mt-24 lg:-mt-28">

        <!-- GIF (samakan dengan 404: lebih besar & fleksibel) -->
        <img src="https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif" alt="Terjadi Kesalahan"
            class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl -mb-8 sm:-mb-10 md:-mb-12 lg:-mb-14">

        <!-- Title -->
        <h1
            class="font-bold text-2xl sm:text-3xl md:text-4xl lg:text-5xl mb-4 text-[#DC2626] text-center relative z-10">
            Terjadi Kesalahan
        </h1>

        <!-- Description -->
        <p class="text-center text-gray-500 text-sm sm:text-base md:text-lg max-w-sm sm:max-w-md mx-auto">
            Mohon maaf, terjadi kesalahan pada sistem. Tim kami sedang bekerja untuk memperbaikinya.
        </p>

        <!-- Tombol -->
        <div class="flex gap-3 mt-6">
            @if (auth()->check())
                <a href="{{ route('landingpage') }}"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition duration-300 shadow-md text-center text-sm sm:text-base"
                    style="background-color: #DC2626;">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            @else
                <a href="/"
                    class="px-6 sm:px-8 py-2.5 sm:py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition duration-300 shadow-md text-center text-sm sm:text-base"
                    style="background-color: #DC2626;">
                    <i class="fas fa-home mr-2"></i> Beranda
                </a>
            @endif

            <!-- Tetap pakai reload karena ini error -->
            <button onclick="location.reload()"
                class="px-6 sm:px-8 py-2.5 sm:py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-300 shadow-md text-center text-sm sm:text-base">
                <i class="fas fa-sync-alt mr-2"></i> Coba Lagi
            </button>
        </div>

        <!-- Info kecil (optional, bisa dipertahankan) -->
        <div class="mt-6 text-center text-xs sm:text-sm text-gray-400">
            <i class="fas fa-info-circle mr-1"></i> Jika masalah berlanjut, hubungi support kami
        </div>

    </section>
</body>

</html>
