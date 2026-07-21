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
                \Filament\Schemas\Components\Group::make([
                    TextInput::make('name')
                        ->label('Nama Turnamen')
                        ->required()
                        ->markAsRequired(false)
                        ->placeholder('Contoh: Turnamen FIFA 24')
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->dehydrated()
                        ->hidden(),

                    TextInput::make('min_slots_per_player')
                        ->label('Minimal Slot per Pemain')
                        ->numeric()
                        ->required()
                        ->markAsRequired(false)
                        ->placeholder('1'),

                    TextInput::make('max_slot_per_player')
                        ->label('Maksimal Slot per Pemain')
                        ->numeric()
                        ->required()
                        ->markAsRequired(false)
                        ->placeholder('5'),

                    TextInput::make('max_entries')
                        ->label('Total Slot Turnamen')
                        ->numeric()
                        ->required()
                        ->markAsRequired(false)
                        ->placeholder('128'),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->markAsRequired(false)
                        ->options([
                            'draft' => 'Draft',
                            'registration' => 'Pendaftaran Buka',
                            'ongoing' => 'Turnamen Berjalan',
                            'completed' => 'Selesai',
                        ])
                        ->default('draft'),
                ]),

                \Filament\Schemas\Components\Group::make([
                    TextInput::make('price_per_slot')
                        ->label('Harga per Slot')
                        ->numeric()
                        ->required()
                        ->markAsRequired(false)
                        ->prefix('Rp')
                        ->placeholder('0'),

                    DateTimePicker::make('registration_start')
                        ->label('Pendaftaran Dibuka')
                        ->required()
                        ->markAsRequired(false),

                    DateTimePicker::make('registration_end')
                        ->label('Pendaftaran Ditutup')
                        ->required()
                        ->markAsRequired(false),

                    DateTimePicker::make('tournament_start')
                        ->label('Turnamen Dimulai')
                        ->required()
                        ->markAsRequired(false),
                ]),

                \Filament\Forms\Components\Hidden::make('check_in_open_minutes_before')
                    ->default(0),

                \Filament\Forms\Components\Hidden::make('tournament_end')
                    ->default(fn () => now()->addYears(5)),

                Textarea::make('payment_info')
                    ->label('Info Pembayaran')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Contoh: Transfer BCA 12345678 a/n Admin'),
            ]);
    }
}
