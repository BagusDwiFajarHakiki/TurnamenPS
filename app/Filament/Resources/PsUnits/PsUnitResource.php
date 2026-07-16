<?php

namespace App\Filament\Resources\PsUnits;

use App\Filament\Resources\PsUnits\Pages\CreatePsUnit;
use App\Filament\Resources\PsUnits\Pages\EditPsUnit;
use App\Filament\Resources\PsUnits\Pages\ListPsUnits;
use App\Filament\Resources\PsUnits\Schemas\PsUnitForm;
use App\Filament\Resources\PsUnits\Tables\PsUnitsTable;
use App\Models\PsUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PsUnitResource extends Resource
{
    protected static ?string $model = PsUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;
    protected static ?string $navigationLabel   = 'Unit PlayStation';
    protected static ?string $modelLabel        = 'Unit PS';
    protected static ?string $pluralModelLabel  = 'Daftar Unit PS';
    protected static ?int    $navigationSort    = 2;

    public static function getNavigationGroup(): ?string { return 'Infrastruktur'; }

    public static function form(Schema $schema): Schema
    {
        return PsUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PsUnitsTable::configure($table);
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
            'index' => ListPsUnits::route('/'),
            'create' => CreatePsUnit::route('/create'),
            'edit' => EditPsUnit::route('/{record}/edit'),
        ];
    }
}
