@extends('auth.layouts.main')
@section('content')

    <body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <form action="{{ route('login.post') }}" method="POST" class="bg-white rounded-md p-6 shadow-sm" autocomplete="off"
                novalidate>
                @csrf
                <h3 class="text-center font-semibold text-sm mb-1">{{ config('app.name') }}</h3>
                <h2 class="text-center font-bold text-base mb-5">Masuk ke akun Anda</h2>

                <label class="block text-[10px] font-normal mb-1" for="email">
                    Alamat email<span class="text-red-600">*</span>
                </label>
                <input id="email" name="email" type="email" value="admin@admin.com" required autofocus
                    class="w-full text-xs rounded border border-gray-200 px-3 py-2 mb-4 focus:outline-none focus:ring-1 focus:ring-red-600" />

                <label class="block text-[10px] font-normal mb-1" for="password">
                    Kata sandi<span class="text-red-600">*</span>
                </label>
                <div class="relative mb-4">
                    <input id="password" name="password" type="password" value="password"
                        class="w-full text-xs rounded border border-gray-200 px-3 py-2 pr-10 focus:outline-none focus:ring-1 focus:ring-red-600" />
                    <button type="button" tabindex="-1"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                {{-- <label class="inline-flex items-center text-[10px] mb-5 select-none">
                    <input type="checkbox" name="remember" class="form-checkbox h-3 w-3 text-gray-600" />
                    <span class="ml-2">Ingat saya</span>
                </label> --}}

                <button type="submit"
                    class="w-full bg-red-600 text-white text-[10px] font-semibold rounded px-3 py-2 hover:bg-red-700 transition-colors">
                    Masuk
                </button>
            </form>

            <p class="text-center text-[10px] mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-red-600 font-semibold hover:underline">Daftar disini</a>
            </p>
        </div>
    </body>
@endsection
