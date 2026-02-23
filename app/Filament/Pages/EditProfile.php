<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Closure;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static string $view = 'filament.pages.edit-profile';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_EditProfile');
    }

    public function mount(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'fullname' => $user->fullname,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data')
            ->model(auth()->user());
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    FileUpload::make('avatar')
                        ->label('Foto Profil')
                        ->image()
                        ->avatar()
                        ->disk('public')
                        ->directory('avatar')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->imageEditor()
                        ->downloadable()
                        ->imageEditorAspectRatios([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->columnSpan(1),

                    Grid::make(1)
                        ->schema([
                            TextInput::make('fullname')
                                ->label('Nama Lengkap')
                                ->required(),

                            TextInput::make('email')
                                ->label('Email')
                                ->required(),

                            TextInput::make('old_password')
                                ->label('Password Lama')
                                ->password()
                                ->placeholder('**********')
                                ->dehydrated(false)
                                ->required(fn($get) => filled($get('password')))
                                ->rule(function ($get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        if (filled($get('password')) && blank($value)) {
                                            $fail('Password lama wajib diisi.');
                                        }

                                        if (filled($get('password')) && ! Hash::check($value, auth()->user()->password)) {
                                            $fail('Password lama tidak cocok.');
                                        }
                                    };
                                }),

                            TextInput::make('password')
                                ->label('Password Baru')
                                ->password()
                                ->placeholder('**********')
                                ->required(fn($get) => filled($get('old_password')))
                                ->rule(function ($get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        if (filled($get('old_password')) && blank($value)) {
                                            $fail('Password baru wajib diisi.');
                                        }

                                        if (filled($value) && Hash::check($value, auth()->user()->password)) {
                                            $fail('Password baru tidak boleh sama dengan password lama.');
                                        }
                                    };
                                })
                                ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn($state) => filled($state))
                                ->nullable(),
                        ])
                        ->columnSpan(2),
                ])
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();
        $user->fullname = $data['fullname'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = $data['password']; // sudah hashed
        }

        if (!empty($data['avatar'])) {
            $user->avatar = is_array($data['avatar']) ? $data['avatar'][0] : $data['avatar'];
        }

        $user->save();

        Notification::make()
            ->title('Profil Diperbarui')
            ->success()
            ->body('Informasi profil berhasil disimpan.')
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
