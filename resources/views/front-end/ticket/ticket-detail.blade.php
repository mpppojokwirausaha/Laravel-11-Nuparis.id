{{-- @dd($ticket) --}}
@extends('front-end.layouts.main')

@section('content')
    @include('front-end.layouts.components.ticket-detail')

    <section class="bg-gray-50 p-6">
        <form class="max-w-5xl mx-auto space-y-6 pt-14">

            <!-- Baris 1: Ticket code & Ticket title -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ticket-code" class="block text-sm font-medium text-gray-700 mb-1">Ticket code</label>
                    <input type="text" id="ticket-code" name="ticket-code" readonly value="{{ $ticket->ticket_code }}"
                        class="block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 cursor-not-allowed" />
                </div>
                <div>
                    <label for="ticket-title" class="block text-sm font-medium text-gray-700 mb-1">Ticket title</label>
                    <input type="text" id="ticket-title" name="ticket-title" readonly value="{{ $ticket->ticket_title }}"
                        class="block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 cursor-not-allowed" />
                </div>
            </div>

            <!-- Baris 2: Ticket WhatsApp & Ticket email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ticket-whatsapp" class="block text-sm font-medium text-gray-700 mb-1">Ticket
                        WhatsApp</label>
                    <input type="text" id="ticket-whatsapp" name="ticket-whatsapp" readonly
                        value="{{ $ticket->ticket_whatsapp ?? '-' }}"
                        class="block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 cursor-not-allowed" />
                </div>
                <div>
                    <label for="ticket-email" class="block text-sm font-medium text-gray-700 mb-1">Ticket email</label>
                    <input type="email" id="ticket-email" name="ticket-email" readonly value="{{ $ticket->ticket_email }}"
                        class="block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 cursor-not-allowed" />
                </div>
            </div>

            <!-- Ticket content -->
            <div>
                <label for="ticket-content" class="block text-sm font-medium text-gray-700 mb-1">Ticket content</label>
                <textarea id="ticket-content" name="ticket-content" readonly rows="10"
                    class="block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 cursor-not-allowed resize-none text-sm leading-relaxed font-sans">{!! $ticket->ticket_content !!}</textarea>
            </div>

            <!-- Dokumen Pendukung Client -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">View Dokumen Pendukung Client</label>
                <div
                    class="flex items-center justify-center w-full h-16 border-2 border-dashed border-gray-300 rounded-md bg-white text-sm text-gray-400 cursor-pointer">
                    Seret &amp; Jatuhkan berkas Anda atau <span class="text-orange-500 ml-1">Jelajahi</span>
                </div>
            </div>

            <!-- Spesialisasi & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                <div>
                    <label for="spesialisasi" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
                    <select id="spesialisasi" name="spesialisasi" disabled
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-700">
                        <option>Aplikasi Website</option>
                        <option>Mobile App</option>
                        <option>Desktop</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span
                            class="text-red-600">*</span></label>
                    <select id="status" name="status" required disabled
                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-700">
                        <option>open</option>
                        <option>closed</option>
                        <option>pending</option>
                    </select>
                </div>
            </div>

            <!-- Daftar Pesan & Dokumen -->
            <div class="space-y-6 p-4 bg-gray-100 rounded-md">
                <!-- Wrapper scrollable dengan tinggi tetap -->
                <div class="h-[780px] overflow-hidden rounded-md shadow relative">
                    <!-- Background dengan opacity -->
                    <div class="absolute inset-0">
                        <div
                            class="w-full h-full bg-[url('{{ asset('assets/front-end/img/bg_chat.png') }}')] bg-cover bg-center opacity-20">
                        </div>
                    </div>

                    <!-- Chat messages container -->
                    <div id="chatMessages" class="relative h-full overflow-y-auto space-y-4 p-4 pr-2">
                        {{-- Loop untuk bubble kiri (progress dari sistem/admin) --}}
                        @foreach ($ticketProgress as $progress)
                            <div class="flex justify-start">
                                <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                                    {{-- Avatar sistem/admin --}}
                                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/fa5848a5-8e02-4e02-99f1-b850d95c0af0.png"
                                        alt="Avatar Admin" class="rounded-full w-6 h-6">

                                    {{-- Bubble pesan kiri --}}
                                    <div
                                        class="message-bubble-receive p-3 rounded-2xl rounded-bl-sm shadow-sm max-w-2xl bg-gray-100">
                                        <div class="text-sm break-words">
                                            {!! $progress['progress'] !!}
                                        </div>

                                        {{-- File lampiran --}}
                                        @if (!empty($progress['file']))
                                            <div class="mt-2">
                                                @foreach ($progress['file'] as $file)
                                                    <a href="{{ asset('storage/' . $file) }}" target="_blank"
                                                        class="text-blue-500 text-xs hover:underline block">
                                                        📎 {{ basename($file) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Timestamp --}}
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($progress['timestamp'])->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Contoh bubble kanan (pesan user) --}}
                        <div class="flex justify-end">
                            <div class="flex items-end space-x-2 max-w-xs lg:max-w-md ">
                                <div
                                    class="message-bubble-send p-3 rounded-2xl rounded-br-sm shadow-sm bg-[#e02020] text-white">
                                    <p class="text-sm text-right">Terima kasih atas informasinya. Akan saya cek kembali.</p>
                                    <p class="text-xs text-gray-300 mt-1 text-right">10:35 AM</p>
                                </div>
                                <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/fa5848a5-8e02-4e02-99f1-b850d95c0af0.png"
                                    alt="Avatar User" class="rounded-full w-6 h-6">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress -->
            <div>
                <label for="progress" class="block text-sm font-medium text-gray-700 mb-1">Progress</label>
                <textarea id="progress" name="progress" rows="5" placeholder="Tulis progress disini..."
                    class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-700 resize-y text-sm font-sans"></textarea>
            </div>

            <!-- Upload File -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload File (Opsional)</label>
                <div
                    class="flex items-center justify-center w-full h-16 border-2 border-dashed border-gray-300 rounded-md bg-white text-sm text-gray-400 cursor-pointer">
                    Seret &amp; Jatuhkan berkas Anda atau <span class="text-orange-500 ml-1">Jelajahi</span>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex space-x-4 justify-start">
                <button type="submit" class="bg-orange-600 text-white px-5 py-2 rounded-md hover:bg-orange-700 transition">
                    Simpan
                </button>
                <button type="button" class="border border-gray-300 px-5 py-2 rounded-md hover:bg-gray-200 transition">
                    Batal
                </button>
            </div>
        </form>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatContainer = document.getElementById('chatMessages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
@endsection
