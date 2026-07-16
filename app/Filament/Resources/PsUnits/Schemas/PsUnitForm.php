<?php

namespace App\Filament\Resources\PsUnits\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class PsUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Unit')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('name')
                    ->label('Nama Console')
                    ->required()
                    ->maxLength(255),

                TextInput::make('location')
                    ->label('Lokasi / TV')
                    ->maxLength(255)
                    ->placeholder('Contoh: TV 1, TV 2, Ruang VIP'),

                Select::make('console_type')
                    ->label('Jenis Console')
                    ->required()
                    ->options([
                        'PS3' => 'PlayStation 3',
                        'PS4' => 'PlayStation 4',
                        'PS5' => 'PlayStation 5',
                    ])
                    ->default('PS3'),

                TextInput::make('controller_count')
                    ->label('Jumlah Stick')
                    ->numeric()
                    ->default(2)
                    ->required(),

                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Aktif (Siap Guna)',
                        'maintenance' => 'Maintenance',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->default('active'),

                Textarea::make('notes')
                    ->label('Catatan Kondisi')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
