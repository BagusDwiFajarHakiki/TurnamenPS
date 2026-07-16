<?php

namespace App\Filament\Resources\Clubs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ClubForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Klub')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('league')
                    ->label('Liga')
                    ->maxLength(255)
                    ->placeholder('Contoh: English Premier League, Serie A, UCL'),

                FileUpload::make('logo')
                    ->label('Logo Klub')
                    ->image()
                    ->disk('public')
                    ->directory('clubs')
                    ->columnSpanFull(),
            ]);
    }
}
