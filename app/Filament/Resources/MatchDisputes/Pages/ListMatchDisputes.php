<?php

namespace App\Filament\Resources\MatchDisputes\Pages;

use App\Filament\Resources\MatchDisputes\MatchDisputeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMatchDisputes extends ListRecords
{
    protected static string $resource = MatchDisputeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
