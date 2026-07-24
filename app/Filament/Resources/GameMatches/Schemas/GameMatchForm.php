<?php

namespace App\Filament\Resources\GameMatches\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class GameMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tournament_stage_id')
                    ->label('Babak')
                    ->relationship('stage', 'name')
                    ->required()
                    ->disabled(),

                TextInput::make('bracket_position')
                    ->label('Posisi Bagan')
                    ->disabled(),

                Select::make('status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'ready' => 'Siap Dimainkan',
                        'scheduled' => 'Terjadwal',
                        'ongoing' => 'Sedang Main',
                        'completed' => 'Selesai',
                        'walkover' => 'Walkover (WO)',
                    ])
                    ->disabled(fn ($record) => $record && $record->is_bye)
                    ->live(),

                Select::make('ps_unit_id')
                    ->label('Unit PlayStation')
                    ->relationship('psUnit', 'name')
                    ->placeholder('Pilih Unit PS ( FIFO )'),

                DateTimePicker::make('scheduled_at')
                    ->label('Jadwal Main'),

                DateTimePicker::make('started_at')
                    ->label('Mulai Main'),

                DateTimePicker::make('finished_at')
                    ->label('Selesai Main'),

                TextInput::make('best_of')
                    ->numeric()
                    ->default(1)
                    ->required(),




                Select::make('no_show_entry_id')
                    ->label('Pemain Tidak Hadir (WO)')
                    ->options(function ($record) {
                        if (!$record) return [];
                        return $record->participants->pluck('entry.display_name', 'tournament_entry_id')->toArray();
                    })
                    ->visible(fn (callable $get) => $get('status') === 'walkover'),

                TextInput::make('walkover_reason')
                    ->label('Alasan WO')
                    ->visible(fn (callable $get) => $get('status') === 'walkover'),

            ]);
    }
}
