<?php

namespace App\Filament\Resources\MatchDisputes;

use App\Filament\Resources\MatchDisputes\Pages\CreateMatchDispute;
use App\Filament\Resources\MatchDisputes\Pages\EditMatchDispute;
use App\Filament\Resources\MatchDisputes\Pages\ListMatchDisputes;
use App\Filament\Resources\MatchDisputes\Schemas\MatchDisputeForm;
use App\Filament\Resources\MatchDisputes\Tables\MatchDisputesTable;
use App\Models\MatchDispute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MatchDisputeResource extends Resource
{
    protected static ?string $model = MatchDispute::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static ?string $navigationLabel   = 'Sengketa Hasil';
    protected static ?string $modelLabel        = 'Sengketa';
    protected static ?string $pluralModelLabel  = 'Daftar Sengketa';
    protected static ?int    $navigationSort    = 2;

    public static function getNavigationGroup(): ?string { return 'Pertandingan'; }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'open')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return MatchDisputeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MatchDisputesTable::configure($table);
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
            'index' => ListMatchDisputes::route('/'),
            'create' => CreateMatchDispute::route('/create'),
            'edit' => EditMatchDispute::route('/{record}/edit'),
        ];
    }
}
