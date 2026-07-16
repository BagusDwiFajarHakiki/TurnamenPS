<?php

namespace App\Filament\Resources\EntryBatches;

use App\Filament\Resources\EntryBatches\Pages\CreateEntryBatch;
use App\Filament\Resources\EntryBatches\Pages\EditEntryBatch;
use App\Filament\Resources\EntryBatches\Pages\ListEntryBatches;
use App\Filament\Resources\EntryBatches\Schemas\EntryBatchForm;
use App\Filament\Resources\EntryBatches\Tables\EntryBatchesTable;
use App\Models\EntryBatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntryBatchResource extends Resource
{
    protected static ?string $model = EntryBatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;
    protected static ?string $navigationLabel   = 'Verifikasi Pembayaran';
    protected static ?string $modelLabel        = 'Pembayaran';
    protected static ?string $pluralModelLabel  = 'Daftar Pembayaran';
    protected static ?int    $navigationSort    = 1;

    public static function getNavigationGroup(): ?string { return 'Peserta & Pembayaran'; }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }


    public static function form(Schema $schema): Schema
    {
        return EntryBatchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntryBatchesTable::configure($table);
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
            'index' => ListEntryBatches::route('/'),
            'create' => CreateEntryBatch::route('/create'),
            'edit' => EditEntryBatch::route('/{record}/edit'),
        ];
    }
}
