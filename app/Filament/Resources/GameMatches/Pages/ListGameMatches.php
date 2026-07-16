<?php

namespace App\Filament\Resources\GameMatches\Pages;

use App\Filament\Resources\GameMatches\GameMatchResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListGameMatches extends ListRecords
{
    protected static string $resource = GameMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('go_to_split_view')
                ->label('Input Hasil Match')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->url(fn() => GameMatchResource::getUrl('input-hasil')),
            CreateAction::make(),
        ];
    }
}
