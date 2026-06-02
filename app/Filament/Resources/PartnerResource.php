<?php

namespace App\Filament\Resources;

use App\Exports\PartnersExport;
use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Info;
use App\Models\Partner;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
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
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';

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
                                Select::make('partner_type')
                                    ->label('Partner Type')
                                    ->placeholder('Pilih minimal 1 opsi')
                                    ->multiple()
                                    ->options([
                                        'Client' => 'Client',
                                        'Supplier' => 'Supplier',
                                    ])
                                    ->required(),
                            ])->columns(3),
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
                                ToggleButtons::make('partner_footer_status')
                                    ->label('Footer Status')
                                    ->options([
                                        'Active' => 'Active',
                                        'Inactive' => 'Inactive',
                                    ])
                                    ->colors([
                                        'Active' => 'success',
                                        'Inactive' => 'danger',
                                    ])
                                    ->grouped()
                                    ->required()
                                    ->rule(function ($record) {
                                        return function ($_, $value, $fail) use ($record) {
                                            if ($value === 'Active') {
                                                $partnerStatus = $record->partner_status ?? request('partner_status');
                                                if ($partnerStatus !== 'Active') {
                                                    $fail('Partner harus Active terlebih dahulu.');
                                                    return;
                                                }
                                                $query = Partner::where('partner_footer_status', 'Active');
                                                if ($record) {
                                                    $query->where('uuid', '!=', $record->uuid);
                                                }
                                                if ($query->count() >= 6) {
                                                    $fail('Maksimal hanya 6 data yang boleh Active.');
                                                }
                                            }
                                        };
                                    }),
                                FileUpload::make('partner_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('img_partners')
                                    ->required()
                                    ->downloadable()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                                        $name = 'partner_' . $get('partner_name') ?: 'partner';
                                        $slug = Str::of($name)
                                            ->lower()
                                            ->replace(' ', '_')
                                            ->slug('_');
                                        return $slug . '_' . time() . '.' . $file->getClientOriginalExtension();
                                    }),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                FileUpload::make('partner_NIB')
                                    ->label('partner NIB')
                                    ->disk('public')
                                    ->directory('NIB_partners')
                                    ->downloadable()
                                    ->dehydrated()
                                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                                        $name = 'partner_NIB_' . $get('partner_name') ?: 'partner_NIB';
                                        $slug = Str::of($name)
                                            ->lower()
                                            ->replace(' ', '_')
                                            ->slug('_');
                                        return $slug . '_' . time() . '.' . $file->getClientOriginalExtension();
                                    }),
                                FileUpload::make('partner_NPWP')
                                    ->label('partner NPWP')
                                    ->disk('public')
                                    ->directory('NPWP_partners')
                                    ->downloadable()
                                    ->dehydrated()
                                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $get): string {
                                        $name = 'partner_NPWP_' . $get('partner_name') ?: 'partner_NPWP';
                                        $slug = Str::of($name)
                                            ->lower()
                                            ->replace(' ', '_')
                                            ->slug('_');
                                        return $slug . '_' . time() . '.' . $file->getClientOriginalExtension();
                                    }),
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
                Action::make('exportExcel')
                    ->label('Export Excel')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function () {
                        $partners = Partner::orderBy('created_at', 'desc')->get();
                        return Excel::download(new PartnersExport($partners, 1), 'partners_export_' . date('Y-m-d_H-i-s') . '.xlsx');
                    }),
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
                                ->default($hasFile ? [$info->partner_guide] : [])
                                ->dehydrated()
                                ->getUploadedFileNameForStorageUsing(
                                    fn($file) => (string) str()->uuid() . '.' . $file->getClientOriginalExtension()
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

                            $oldFile = $info->partner_guide;
                            if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                                Storage::disk('public')->delete($oldFile);
                            }

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
                                        if (Storage::disk('public')->exists($info->partner_guide)) {
                                            Storage::disk('public')->delete($info->partner_guide);
                                        }
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
                TextColumn::make('no')
                    ->label('NO')
                    ->state(function ($rowLoop): string {
                        return (string) $rowLoop->iteration;
                    })
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->width('50px')
                    ->alignment('center'),

                ImageColumn::make('partner_image')
                    ->label('IMAGE')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('partner_name')
                    ->label('NAME')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('partner_phone')
                    ->label('PHONE')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('partner_email')
                    ->label('EMAIL')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('partner_email')
                    ->label('EMAIL')
                    ->searchable()
                    ->url(fn($record) => $record->partner_email ? "mailto:{$record->partner_email}" : null)
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-envelope')
                    ->iconColor('info')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('partner_type')
                    ->label('TYPE')
                    ->badge()
                    ->colors(['primary' => 'Client', 'danger' => 'Supplier'])
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->sortable()
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
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('partner_footer_status')
                    ->label('SHOW FOOTER')
                    ->icon(fn(string $state): string => match ($state) {
                        'Active' => 'heroicon-o-check-circle',
                        'Inactive' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'success',
                        'Inactive' => 'danger',
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('LOG')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Action::make('view_npwp')
                    ->label('NPWP')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn($record) => $record->partner_NPWP ? Storage::url($record->partner_NPWP) : null)
                    ->openUrlInNewTab()
                    ->disabled(fn($record) => empty($record->partner_NPWP)),

                Action::make('view_nib')
                    ->label('NIB')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn($record) => $record->partner_NIB ? Storage::url($record->partner_NIB) : null)
                    ->openUrlInNewTab()
                    ->disabled(fn($record) => empty($record->partner_NIB)),

                Action::make('visit')
                    ->icon('heroicon-o-link')
                    ->url(fn($record) => $record->partner_url)
                    ->openUrlInNewTab()
                    ->disabled(fn($record) => empty($record->partner_url)),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Action::make('exportSelected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($livewire) {
                            $partners = $livewire->getSelectedTableRecords();

                            // Hitung nomor awal berdasarkan urutan data di database
                            $allPartners = Partner::orderBy('created_at', 'desc')->pluck('id')->toArray();
                            $selectedIds = $partners->pluck('id')->toArray();

                            $firstSelectedIndex = array_search($selectedIds[0], $allPartners);
                            $startNumber = $firstSelectedIndex + 1;

                            return Excel::download(new PartnersExport($partners, $startNumber), 'selected_partners_export_' . date('Y-m-d_H-i-s') . '.xlsx');
                        }),
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
        return [];
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
