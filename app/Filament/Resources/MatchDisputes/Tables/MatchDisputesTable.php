<?php

namespace App\Filament\Resources\MatchDisputes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class MatchDisputesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('match.bracket_position')
                    ->label('Match Posisi')
                    ->sortable(),

                TextColumn::make('entry.display_name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'reviewing' => 'warning',
                        'upheld' => 'success',
                        'rejected' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('reviewer.name')
                    ->label('Ditinjau Oleh')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('resolved_at')
                    ->label('Waktu Selesai')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dilaporkan Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Terbuka',
                        'reviewing' => 'Sedang Ditinjau',
                        'upheld' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
