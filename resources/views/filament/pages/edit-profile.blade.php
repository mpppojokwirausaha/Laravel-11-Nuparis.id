<x-filament::page>
    @php
        $avatar = $data['avatar'] ?? auth()->user()->avatar;
        $avatarPath = is_array($avatar) ? $avatar[0] ?? null : $avatar;
    @endphp

    {{-- Kolom Form --}}
    <div class="col-span-2">
        <form wire:submit.prevent="submit" class="space-y-6">
            {{ $this->form }}

            <x-filament::button type="submit">
                Simpan Perubahan
            </x-filament::button>
        </form>
    </div>
</x-filament::page>
