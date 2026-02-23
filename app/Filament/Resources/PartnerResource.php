<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use App\Models\Info;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Group;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('partner_name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('partner_slug', Str::slug($state)))
                                    ->required(),
                                TextInput::make('partner_slug')
                                    ->required()
                                    ->placeholder('Auto Generated'),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('partner_phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('partner_email')
                                    ->email()
                                    ->placeholder('example@gmail.com')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('partner_url')
                                    ->url()
                                    ->prefix('https://')
                                    ->maxLength(255),
                            ])->columns(3),
                        Group::make()
                            ->schema([
                                ToggleButtons::make('partner_status')
                                    ->options([
                                        'Active' => 'Active',
                                        'Inactive' => 'Inactive',
                                    ])
                                    ->colors([
                                        'Active' => 'success',
                                        'Inactive' => 'danger',
                                    ])->grouped()
                                    ->required(),
                                FileUpload::make('partner_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('img_partners')
                                    ->required()
                                    ->downloadable()
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->dehydrated()
                                    ->getUploadedFileNameForStorageUsing(
                                        fn ($file) => (string) str()->uuid() . '.' . $file->getClientOriginalExtension()
                                    ),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                FileUpload::make('partner_NIB')
                                    ->label('partner NIB')
                                    ->disk('public')
                                    ->directory('NIB_partners')
                                    ->downloadable()
                                    ->dehydrated()
                                    ->getUploadedFileNameForStorageUsing(
                                        fn ($file) => (string) str()->uuid() . '.' . $file->getClientOriginalExtension()
                                    ),
                                FileUpload::make('partner_NPWP')
                                    ->label('partner NPWP')
                                    ->disk('public')
                                    ->directory('NPWP_partners')
                                    ->downloadable()
                                    ->dehydrated()
                                    ->getUploadedFileNameForStorageUsing(
                                        fn ($file) => (string) str()->uuid() . '.' . $file->getClientOriginalExtension()
                                    ),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                RichEditor::make('partner_description')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'link',
                                        'orderedList',
                                        'underline',
                                        'undo',
                                    ])
                                    ->required(),
                                RichEditor::make('partner_address')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'link',
                                        'orderedList',
                                        'underline',
                                        'undo',
                                    ])
                                    ->required(),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('uploadPanduanMitra')
                    ->label(function () {
                        $info = Info::first();
                        return $info && $info->partner_guide ? 'Update Panduan Mitra' : 'Upload Panduan Mitra';
                    })
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-up')
                    ->modalHeading(function () {
                        $info = Info::first();
                        return $info && $info->partner_guide ? 'Update Panduan Mitra' : 'Upload Panduan Mitra';
                    })
                    ->form(function () {
                        $info = Info::first();
                        $hasFile = $info && $info->partner_guide;
                        
                        return [
                            FileUpload::make('partner_guide')
                                ->label($hasFile ? 'File Panduan Baru (PDF)' : 'File Panduan Mitra (PDF)')
                                ->required()
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(5120)
                                ->disk('public')
                                ->directory('panduan-mitra')
                                ->storeFiles()
                                ->preserveFilenames()
                                // Tampilkan file yang sudah ada di FilePond
                                ->default($hasFile ? [$info->partner_guide] : [])
                                ->dehydrated()
                                ->getUploadedFileNameForStorageUsing(
                                    fn ($file) => (string) str()->uuid() . '.' . $file->getClientOriginalExtension()
                                )
                                ->helperText('Format PDF. Maksimal 5MB')
                        ];
                    })
                    ->action(function (array $data) {
                        try {
                            $info = Info::first();
                            
                            if (!$info) {
                                $info = Info::create([
                                    'uuid' => Str::uuid(),
                                    'address' => '-',
                                    'email' => 'admin@example.com',
                                    'instagram' => '-',
                                    'youtube' => '-',
                                    'logo' => 'default.png',
                                    'meta_domain' => 'example.com',
                                    'meta_title' => 'Default',
                                    'meta_desc' => 'Default',
                                    'meta_keywords' => 'default',
                                    'meta_image' => 'default.png',
                                ]);
                            }
                            
                            // Hapus file lama jika ada
                            $oldFile = $info->partner_guide;
                            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                                Storage::disk('public')->delete($oldFile);
                            }
                            
                            // Update database
                            $info->update(['partner_guide' => $data['partner_guide']]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('✅ Berhasil!')
                                ->body('Panduan mitra telah ' . ($oldFile ? 'diupdate' : 'diupload'))
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('❌ Error!')
                                ->body('Gagal: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->extraModalFooterActions([
                        Action::make('hapusFile')
                            ->label('Hapus File')
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus File Panduan')
                            ->modalDescription('File akan dihapus permanen dari sistem. Anda tidak dapat mengembalikannya.')
                            ->action(function () {
                                try {
                                    $info = Info::first();
                                    
                                    if ($info && $info->partner_guide) {
                                        $filename = basename($info->partner_guide);
                                        
                                        // Hapus dari storage
                                        if (Storage::disk('public')->exists($info->partner_guide)) {
                                            Storage::disk('public')->delete($info->partner_guide);
                                        }
                                        
                                        // Hapus dari database
                                        $info->update(['partner_guide' => null]);
                                        
                                        \Filament\Notifications\Notification::make()
                                            ->title('✅ Berhasil!')
                                            ->body('File ' . $filename . ' telah dihapus')
                                            ->success()
                                            ->send();
                                    }
                                } catch (\Exception $e) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('❌ Error!')
                                        ->body('Gagal menghapus: ' . $e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->hidden(fn() => !Info::first()?->partner_guide),
                    ]),
            ])
            ->columns([
                ImageColumn::make('partner_image')
                    ->label('IMAGE')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('partner_name')
                    ->label('NAME')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('partner_phone')
                    ->label('PHONE')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('partner_email')
                    ->label('EMAIL')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('partner_status')
                    ->label('STATUS')
                    ->icon(fn(string $state): string => match ($state) {
                        'Active' => 'heroicon-o-check-circle',
                        'Inactive' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                    })->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('LOG')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create Partner')
                    ->url(route('filament.management.resources.partners.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}