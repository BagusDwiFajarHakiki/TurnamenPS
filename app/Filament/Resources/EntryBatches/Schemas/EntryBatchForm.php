<?php

namespace App\Filament\Resources\EntryBatches\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;

class EntryBatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(['default' => 1, 'md' => 3])
                    ->columnSpan('full')
                    ->schema([
                    \Filament\Schemas\Components\Section::make('Informasi Pendaftaran')
                        ->columnSpan(['default' => 1, 'md' => 2])
                        ->columns(2)
                        ->schema([
                            Select::make('tournament_id')
                                ->relationship('tournament', 'name')
                                ->required(),

                            Select::make('player_id')
                                ->relationship('player', 'name')
                                ->required(),

                            TextInput::make('slot_count')
                                ->label('Jumlah Slot')
                                ->numeric()
                                ->required()
                                ->default(1),

                            TextInput::make('total_price')
                                ->label('Total Harga')
                                ->numeric()
                                ->required()
                                ->prefix('Rp'),
                        ]),

                    \Filament\Schemas\Components\Section::make('Pembayaran & Status')
                        ->columnSpan(['default' => 1, 'md' => 1])
                        ->schema([
                            Select::make('status')
                                ->required()
                                ->options([
                                    'pending' => 'Pending',
                                    'verified' => 'Diverifikasi (Aktifkan Slot)',
                                    'rejected' => 'Ditolak',
                                ])
                                ->default('pending')
                                ->live(),

                            Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'qris' => 'QRIS',
                                    'cash' => 'Cash / Tunai',
                                ])
                                ->required()
                                ->live()
                                ->default('qris'),

                            FileUpload::make('payment_proof_path')
                                ->label('Bukti Pembayaran QRIS')
                                ->image()
                                ->disk('public')
                                ->directory('payments')
                                ->maxSize(10240)
                                ->openable()
                                ->previewable()
                                ->visible(fn (callable $get) => $get('payment_method') === 'qris'),

                            Textarea::make('rejection_reason')
                                ->label('Alasan Penolakan')
                                ->maxLength(65535)
                                ->visible(fn (callable $get) => $get('status') === 'rejected')
                                ->required(fn (callable $get) => $get('status') === 'rejected'),
                        ])
                ])
            ]);
    }
}
