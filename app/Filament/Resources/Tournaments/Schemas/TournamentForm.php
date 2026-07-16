<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->dehydrated()
                    ->hidden(),

                TextInput::make('game_title')
                    ->required()
                    ->default('eFootball / PES')
                    ->maxLength(255),

                TextInput::make('price_per_slot')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->default(0.00),

                TextInput::make('max_slot_per_player')
                    ->numeric()
                    ->required()
                    ->default(5),

                TextInput::make('max_entries')
                    ->numeric()
                    ->required()
                    ->default(128),





                TextInput::make('check_in_open_minutes_before')
                    ->label('Waktu Check-In Dibuka (Menit)')
                    ->numeric()
                    ->required()
                    ->default(120)
                    ->helperText('Berapa menit check-in dibuka sebelum waktu pertandingan dimulai'),

                DateTimePicker::make('registration_start')
                    ->required(),

                DateTimePicker::make('registration_end')
                    ->required(),

                DateTimePicker::make('tournament_start')
                    ->required(),

                DateTimePicker::make('tournament_end')
                    ->required(),

                Select::make('status')
                    ->required()
                    ->options([
                        'draft' => 'Draft',
                        'registration' => 'Pendaftaran Buka',
                        'ongoing' => 'Turnamen Berjalan',
                        'completed' => 'Selesai',
                    ])
                    ->default('draft'),

                Textarea::make('payment_info')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Contoh: Transfer BCA 12345678 a/n Admin'),

                RichEditor::make('rules_content')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
