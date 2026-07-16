<?php

namespace App\Filament\Widgets;

use App\Models\EntryBatch;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = '💳 Pembayaran Menunggu Verifikasi';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EntryBatch::query()
                    ->where('status', 'pending')
                    ->with(['player', 'tournament'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('player.name')
                    ->label('Peserta')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tournament.name')
                    ->label('Turnamen')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('slot_count')
                    ->label('Slot')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->since()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'  => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('verify')
                    ->label('✅ Verifikasi')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (EntryBatch $record) => $record->update(['status' => 'verified']))
                    ->visible(fn (EntryBatch $record) => $record->status === 'pending'),
                \Filament\Actions\Action::make('reject')
                    ->label('❌ Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (EntryBatch $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (EntryBatch $record) => $record->status === 'pending'),
            ])
            ->emptyStateHeading('Tidak ada pembayaran pending')
            ->emptyStateDescription('Semua pembayaran sudah diproses.')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
