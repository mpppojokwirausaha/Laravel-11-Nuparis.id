<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Info;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Pengaturan Website';
    protected static ?int $navigationSort = 60;
    protected static string $view = 'filament.pages.settings';
    protected static ?string $navigationGroup = 'Settings';

    public Info $info;

    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('page_Settings');
    }

    public function mount(): void
    {
        $this->info = Info::firstOrFail();
        $this->form->fill($this->info->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Website Settings')
                    ->description('section untuk mengatur informasi website')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('no_whatsapp')
                                ->label('No. Telepon')
                                ->required(),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required(),

                            TextInput::make('youtube')
                                ->label('Youtube')
                                ->required(),

                            TextInput::make('instagram')
                                ->label('Instagram')
                                ->required(),

                            Textarea::make('address')
                                ->label('Alamat')
                                ->required()
                                ->columnSpanFull()
                                ->autosize(),
                            RichEditor::make('privacy_policy')
                                ->toolbarButtons([
                                    'bold',
                                    'bulletList',
                                    'link',
                                    'orderedList',
                                    'underline',
                                    'undo',
                                ])->columnSpanFull(),
                            RichEditor::make('terms_conditions')
                                ->toolbarButtons([
                                    'bold',
                                    'bulletList',
                                    'link',
                                    'orderedList',
                                    'underline',
                                    'undo',
                                ])->columnSpanFull(),
                        ]),
                    ])->columns(2),

                Section::make('Meta Settings')
                    ->description('section untuk mengatur kebutuhan meta data')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('meta_domain')
                                ->label('Domain')
                                ->prefix('https://')
                                ->url()
                                ->required(),

                            TextInput::make('meta_title')
                                ->label('Title')
                                ->required(),

                            TextArea::make('meta_desc')
                                ->label('Description')
                                ->required()
                                ->autosize(),

                            FileUpload::make('meta_image')
                                ->label('Image')
                                ->image()
                                ->disk('public')->directory('meta')
                                ->required()
                                ->downloadable()
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ]),
                        ]),
                    ])
            ])

            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        // dd($data);
        $this->info->update($data);

        Notification::make()
            ->title('Pengaturan Diperbarui')
            ->success()
            ->body('Informasi pengaturan berhasil disimpan.')
            ->send();
    }
}
