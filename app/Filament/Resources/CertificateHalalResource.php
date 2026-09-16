<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateHalalResource\Pages;
use App\Exports\HalalExport;
use App\Models\Halal;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class CertificateHalalResource extends Resource
{
    protected static ?string $model = Halal::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Sertifikat Halal';
    protected static ?string $modelLabel = 'Sertifikat Halal';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Pelaku Usaha')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_pelaku')->label('Nama Pelaku'),
                        Infolists\Components\TextEntry::make('nama_brand')->label('Nama Brand'),
                        Infolists\Components\TextEntry::make('nik_ktp')->label('NIK KTP'),
                        Infolists\Components\TextEntry::make('nib')->label('NIB'),
                        Infolists\Components\TextEntry::make('npwp')->label('NPWP'),
                        Infolists\Components\TextEntry::make('no_whatsapp')->label('No. WhatsApp'),
                        Infolists\Components\TextEntry::make('email'),
                        Infolists\Components\TextEntry::make('alamat')->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Data Usaha')
                    ->schema([
                        Infolists\Components\TextEntry::make('modal_awal')->label('Modal Awal')->money('IDR'),
                        Infolists\Components\TextEntry::make('tahun_berdiri')->label('Tahun Berdiri'),
                        Infolists\Components\TextEntry::make('luas_usaha')->label('Luas Usaha'),
                        Infolists\Components\TextEntry::make('pendapatan_minggu')->label('Pendapatan/Minggu')->money('IDR'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Produk')
                    ->schema([
                        Infolists\Components\TextEntry::make('bahan')
                            ->label('Bahan')
                            ->html()
                            ->formatStateUsing(fn($state) => nl2br(e($state)))
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('cara_pembuatan')
                            ->label('Cara Pembuatan')
                            ->html()
                            ->formatStateUsing(fn($state) => nl2br(e($state)))
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_brand')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_pelaku')
                    ->label('Pelaku Usaha')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nik_ktp')
                    ->label('KTP NIK')
                    ->searchable(),

                Tables\Columns\TextColumn::make('npwp')
                    ->label('NPWP')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nib')
                    ->label('NIB')
                    ->searchable(),

                // Tables\Columns\BadgeColumn::make('status')
                //     ->colors([
                //         'gray'    => 'pending',
                //         'warning' => 'diproses',
                //         'success' => 'disetujui',
                //         'danger'  => 'ditolak',
                //     ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'diproses'  => 'Diproses',
                        'disetujui' => 'Disetujui',
                        'ditolak'   => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('deleteWithPassword')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data')
                    ->modalDescription('Masukkan password admin untuk konfirmasi penghapusan data ini.')
                    ->modalSubmitActionLabel('Hapus')
                    ->form([
                        Forms\Components\TextInput::make('admin_password')
                            ->label('Password Admin')
                            ->password()
                            ->revealable()
                            ->required(),
                    ])
                    ->action(function (array $data, Halal $record) {
                        if (! Hash::check($data['admin_password'], Auth::user()->password)) {
                            Notification::make()
                                ->title('Password salah')
                                ->body('Data tidak jadi dihapus.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Data berhasil dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportExcelSelected')
                        ->label('Export Excel (Terpilih)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            return Excel::download(
                                new HalalExport($records),
                                'sertifikat-halal-terpilih-' . now()->format('Ymd-His') . '.xlsx'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('deleteSelectedWithPassword')
                        ->label('Hapus Terpilih')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data Terpilih')
                        ->modalDescription('Masukkan password admin untuk konfirmasi penghapusan data yang dipilih.')
                        ->modalSubmitActionLabel('Hapus')
                        ->form([
                            Forms\Components\TextInput::make('admin_password')
                                ->label('Password Admin')
                                ->password()
                                ->revealable()
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            if (! Hash::check($data['admin_password'], Auth::user()->password)) {
                                Notification::make()
                                    ->title('Password salah')
                                    ->body('Data tidak jadi dihapus.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $records->each->delete();

                            Notification::make()
                                ->title('Data terpilih berhasil dihapus')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportExcelByDate')
                    ->label('Export Excel (by Tanggal)')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->required(),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Sampai Tanggal')
                            ->required()
                            ->afterOrEqual('date_from'),
                    ])
                    ->action(function (array $data) {
                        $halals = Halal::query()
                            ->whereDate('created_at', '>=', $data['date_from'])
                            ->whereDate('created_at', '<=', $data['date_to'])
                            ->orderBy('created_at')
                            ->get();

                        return Excel::download(
                            new HalalExport($halals),
                            'sertifikat-halal-' . $data['date_from'] . '-sd-' . $data['date_to'] . '.xlsx'
                        );
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificateHalals::route('/'),
            'view'  => Pages\ViewCertificateHalal::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
