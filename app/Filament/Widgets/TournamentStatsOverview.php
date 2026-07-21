<?php

namespace App\Filament\Widgets;

use App\Models\Tournament;
use App\Models\TournamentEntry;
use App\Models\EntryBatch;
use App\Models\GameMatch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TournamentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeTournament = Tournament::whereIn('status', ['registration', 'ongoing'])->latest()->first();

        $totalSlots = $activeTournament
            ? TournamentEntry::where('tournament_id', $activeTournament->id)->count()
            : 0;

        $pendingPayments = EntryBatch::where('status', 'pending')->count();

        $ongoingMatches = GameMatch::whereIn('status', ['scheduled', 'ongoing'])->count();

        $completedMatches = $activeTournament
            ? GameMatch::whereHas('stage', fn($q) => $q->where('tournament_id', $activeTournament->id))
                ->where('status', 'completed')->count()
            : 0;

        return [
            Stat::make('Turnamen Aktif', $activeTournament?->name ?? 'Tidak ada')
                ->description($activeTournament ? '🟢 ' . ucfirst($activeTournament->status) : '—')
                ->color($activeTournament ? 'success' : 'gray')
                ->icon('heroicon-o-trophy'),

            Stat::make('Total Peserta Terdaftar', $totalSlots . ' slot')
                ->description('Pada turnamen aktif')
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Pembayaran Pending', $pendingPayments)
                ->description($pendingPayments > 0 ? 'Butuh verifikasi' : 'Semua terverifikasi')
                ->color($pendingPayments > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Pertandingan Berlangsung', $ongoingMatches)
                ->description("{$completedMatches} match sudah selesai")
                ->color('info')
                ->icon('heroicon-o-play-circle'),
        ];
    }
}
