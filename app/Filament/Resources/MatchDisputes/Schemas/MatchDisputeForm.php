<?php

namespace App\Filament\Resources\MatchDisputes\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class MatchDisputeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('match_id')
                    ->label('Pertandingan')
                    ->relationship('match', 'bracket_position')
                    ->disabled()
                    ->dehydrated(),

                Select::make('raised_by_entry_id')
                    ->label('Pelapor (Slot)')
                    ->relationship('entry', 'display_name')
                    ->disabled()
                    ->dehydrated(),

                Textarea::make('reason')
                    ->label('Alasan Sengketa')
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),

                Select::make('status')
                    ->required()
                    ->options([
                        'open' => 'Terbuka',
                        'reviewing' => 'Sedang Ditinjau',
                        'upheld' => 'Disetujui (Ganti Skor/WO)',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('open'),

                Textarea::make('resolution_note')
                    ->label('Catatan Resolusi')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
