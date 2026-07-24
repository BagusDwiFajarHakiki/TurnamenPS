<?php

namespace App\Filament\Resources\Tournaments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;

class TournamentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Dasar')->schema([
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

                    \Filament\Schemas\Components\Grid::make(3)->schema([
                        TextInput::make('min_slots_per_player')
                            ->label('Min Slot')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->markAsRequired(false)
                            ->placeholder('1')
                            ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*', 'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"]),

                        TextInput::make('max_slot_per_player')
                            ->label('Max Slot')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->markAsRequired(false)
                            ->placeholder('5')
                            ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*', 'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"]),

                        TextInput::make('max_entries')
                            ->label('Total Slot')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->markAsRequired(false)
                            ->placeholder('128')
                            ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*', 'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"]),
                    ]),

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
                ])->columnSpan(1),

                \Filament\Schemas\Components\Section::make('Waktu & Biaya')->schema([
                    \Filament\Schemas\Components\Grid::make(2)->schema([
                        TextInput::make('price_per_slot')
                            ->label('Harga per Slot')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->markAsRequired(false)
                            ->prefix('Rp')
                            ->placeholder('0')
                            ->extraInputAttributes(['inputmode' => 'numeric', 'pattern' => '[0-9]*', 'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"]),

                        DateTimePicker::make('tournament_start')
                            ->label('Turnamen Dimulai')
                            ->required()
                            ->markAsRequired(false),
                    ]),

                    \Filament\Schemas\Components\Grid::make(2)->schema([
                        DateTimePicker::make('registration_start')
                            ->label('Pendaftaran Dibuka')
                            ->required()
                            ->markAsRequired(false),

                        DateTimePicker::make('registration_end')
                            ->label('Pendaftaran Ditutup')
                            ->required()
                            ->markAsRequired(false),
                    ]),

                    FileUpload::make('qris_image_path')
                        ->label('Foto QRIS Pembayaran')
                        ->image()
                        ->imageEditor()
                        ->directory('qris')
                        ->disk('public')
                        ->maxSize(10240),
                ])->columnSpan(1),

                \Filament\Forms\Components\Hidden::make('check_in_open_minutes_before')
                    ->default(0),

                \Filament\Forms\Components\Hidden::make('tournament_end')
                    ->default(fn () => now()->addYears(5)),
            ]);
    }
}
