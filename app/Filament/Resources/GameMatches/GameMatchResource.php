<?php

namespace App\Filament\Resources\GameMatches;

use App\Filament\Resources\GameMatches\Pages\CreateGameMatch;
use App\Filament\Resources\GameMatches\Pages\EditGameMatch;
use App\Filament\Resources\GameMatches\Pages\ListGameMatches;
use App\Filament\Resources\GameMatches\Pages\InputHasil;
use App\Filament\Resources\GameMatches\Schemas\GameMatchForm;
use App\Filament\Resources\GameMatches\Tables\GameMatchesTable;
use App\Models\GameMatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GameMatchResource extends Resource
{
    protected static ?string $model = GameMatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;
    protected static ?string $navigationLabel   = 'Input Hasil Match';
    protected static ?string $modelLabel        = 'Pertandingan';
    protected static ?string $pluralModelLabel  = 'Semua Pertandingan';
    protected static ?int    $navigationSort    = 1;

    public static function getNavigationGroup(): ?string { return 'Pertandingan'; }

    public static function form(Schema $schema): Schema
    {
        return GameMatchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GameMatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => InputHasil::route('/'),
            'create' => CreateGameMatch::route('/create'),
            'edit' => EditGameMatch::route('/{record}/edit'),
        ];
    }
}
