<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed eFootball Clubs
        $clubs = [
            // ── Premier League ──
            ['name' => 'Manchester City', 'league' => 'Premier League'],
            ['name' => 'Arsenal FC', 'league' => 'Premier League'],
            ['name' => 'Liverpool FC', 'league' => 'Premier League'],
            ['name' => 'Manchester United', 'league' => 'Premier League'],
            ['name' => 'Chelsea FC', 'league' => 'Premier League'],
            ['name' => 'Tottenham Hotspur', 'league' => 'Premier League'],
            ['name' => 'Newcastle United', 'league' => 'Premier League'],
            ['name' => 'Aston Villa', 'league' => 'Premier League'],
            ['name' => 'Brighton & Hove Albion', 'league' => 'Premier League'],
            ['name' => 'West Ham United', 'league' => 'Premier League'],
            ['name' => 'Brentford FC', 'league' => 'Premier League'],
            ['name' => 'Crystal Palace', 'league' => 'Premier League'],
            ['name' => 'Fulham FC', 'league' => 'Premier League'],
            ['name' => 'Wolverhampton Wanderers', 'league' => 'Premier League'],
            ['name' => 'Nottingham Forest', 'league' => 'Premier League'],
            ['name' => 'Bournemouth AFC', 'league' => 'Premier League'],
            ['name' => 'Everton FC', 'league' => 'Premier League'],
            ['name' => 'Leicester City', 'league' => 'Premier League'],
            ['name' => 'Ipswich Town', 'league' => 'Premier League'],
            ['name' => 'Southampton FC', 'league' => 'Premier League'],

            // ── La Liga ──
            ['name' => 'Real Madrid', 'league' => 'La Liga'],
            ['name' => 'FC Barcelona', 'league' => 'La Liga'],
            ['name' => 'Atletico Madrid', 'league' => 'La Liga'],
            ['name' => 'Athletic Bilbao', 'league' => 'La Liga'],
            ['name' => 'Real Sociedad', 'league' => 'La Liga'],
            ['name' => 'Real Betis', 'league' => 'La Liga'],
            ['name' => 'Villarreal CF', 'league' => 'La Liga'],
            ['name' => 'Girona FC', 'league' => 'La Liga'],
            ['name' => 'Sevilla FC', 'league' => 'La Liga'],
            ['name' => 'Valencia CF', 'league' => 'La Liga'],
            ['name' => 'Celta Vigo', 'league' => 'La Liga'],
            ['name' => 'RCD Mallorca', 'league' => 'La Liga'],
            ['name' => 'UD Las Palmas', 'league' => 'La Liga'],
            ['name' => 'CA Osasuna', 'league' => 'La Liga'],
            ['name' => 'Getafe CF', 'league' => 'La Liga'],
            ['name' => 'RCD Espanyol', 'league' => 'La Liga'],
            ['name' => 'Deportivo Alaves', 'league' => 'La Liga'],
            ['name' => 'Real Valladolid', 'league' => 'La Liga'],
            ['name' => 'UD Almeria', 'league' => 'La Liga'],
            ['name' => 'CD Leganes', 'league' => 'La Liga'],

            // ── Bundesliga ──
            ['name' => 'Bayern Munich', 'league' => 'Bundesliga'],
            ['name' => 'Bayer Leverkusen', 'league' => 'Bundesliga'],
            ['name' => 'Borussia Dortmund', 'league' => 'Bundesliga'],
            ['name' => 'VfB Stuttgart', 'league' => 'Bundesliga'],
            ['name' => 'RB Leipzig', 'league' => 'Bundesliga'],
            ['name' => 'Eintracht Frankfurt', 'league' => 'Bundesliga'],
            ['name' => 'VfL Wolfsburg', 'league' => 'Bundesliga'],
            ['name' => 'SC Freiburg', 'league' => 'Bundesliga'],
            ['name' => 'Borussia Monchengladbach', 'league' => 'Bundesliga'],
            ['name' => '1. FC Union Berlin', 'league' => 'Bundesliga'],
            ['name' => 'Werder Bremen', 'league' => 'Bundesliga'],
            ['name' => '1. FC Heidenheim', 'league' => 'Bundesliga'],
            ['name' => '1. FC Koln', 'league' => 'Bundesliga'],
            ['name' => 'VfL Bochum', 'league' => 'Bundesliga'],
            ['name' => 'TSG Hoffenheim', 'league' => 'Bundesliga'],
            ['name' => 'FC Augsburg', 'league' => 'Bundesliga'],
            ['name' => 'Mainz 05', 'league' => 'Bundesliga'],
            ['name' => 'FC St. Pauli', 'league' => 'Bundesliga'],
            ['name' => 'Holstein Kiel', 'league' => 'Bundesliga'],

            // ── Serie A ──
            ['name' => 'Inter Milan', 'league' => 'Serie A'],
            ['name' => 'AC Milan', 'league' => 'Serie A'],
            ['name' => 'Juventus FC', 'league' => 'Serie A'],
            ['name' => 'SSC Napoli', 'league' => 'Serie A'],
            ['name' => 'AS Roma', 'league' => 'Serie A'],
            ['name' => 'SS Lazio', 'league' => 'Serie A'],
            ['name' => 'Atalanta BC', 'league' => 'Serie A'],
            ['name' => 'ACF Fiorentina', 'league' => 'Serie A'],
            ['name' => 'Bologna FC', 'league' => 'Serie A'],
            ['name' => 'Torino FC', 'league' => 'Serie A'],
            ['name' => 'US Sassuolo', 'league' => 'Serie A'],
            ['name' => 'Genoa CFC', 'league' => 'Serie A'],
            ['name' => 'Cagliari Calcio', 'league' => 'Serie A'],
            ['name' => 'Udinese Calcio', 'league' => 'Serie A'],
            ['name' => 'US Lecce', 'league' => 'Serie A'],
            ['name' => 'Hellas Verona', 'league' => 'Serie A'],
            ['name' => 'Parma Calcio', 'league' => 'Serie A'],
            ['name' => 'Como 1907', 'league' => 'Serie A'],
            ['name' => 'Venezia FC', 'league' => 'Serie A'],
            ['name' => 'Empoli FC', 'league' => 'Serie A'],

            // ── Ligue 1 ──
            ['name' => 'Paris Saint-Germain', 'league' => 'Ligue 1'],
            ['name' => 'Olympique Marseille', 'league' => 'Ligue 1'],
            ['name' => 'AS Monaco', 'league' => 'Ligue 1'],
            ['name' => 'Olympique Lyonnais', 'league' => 'Ligue 1'],
            ['name' => 'Lille OSC', 'league' => 'Ligue 1'],
            ['name' => 'OGC Nice', 'league' => 'Ligue 1'],
            ['name' => 'RC Lens', 'league' => 'Ligue 1'],
            ['name' => 'RC Strasbourg', 'league' => 'Ligue 1'],
            ['name' => 'Nantes FC', 'league' => 'Ligue 1'],
            ['name' => 'Stade Brestois', 'league' => 'Ligue 1'],
            ['name' => 'Toulouse FC', 'league' => 'Ligue 1'],
            ['name' => 'Le Havre AC', 'league' => 'Ligue 1'],
            ['name' => 'Montpellier HSC', 'league' => 'Ligue 1'],
            ['name' => 'AS Saint-Etienne', 'league' => 'Ligue 1'],
            ['name' => 'AJ Auxerre', 'league' => 'Ligue 1'],
            ['name' => 'Angers SCO', 'league' => 'Ligue 1'],
            ['name' => 'Stade de Reims', 'league' => 'Ligue 1'],

            // ── Eredivisie ──
            ['name' => 'PSV Eindhoven', 'league' => 'Eredivisie'],
            ['name' => 'Ajax Amsterdam', 'league' => 'Eredivisie'],
            ['name' => 'Feyenoord Rotterdam', 'league' => 'Eredivisie'],
            ['name' => 'FC Twente', 'league' => 'Eredivisie'],
            ['name' => 'AZ Alkmaar', 'league' => 'Eredivisie'],
            ['name' => 'FC Utrecht', 'league' => 'Eredivisie'],

            // ── Primeira Liga ──
            ['name' => 'SL Benfica', 'league' => 'Primeira Liga'],
            ['name' => 'FC Porto', 'league' => 'Primeira Liga'],
            ['name' => 'Sporting CP', 'league' => 'Primeira Liga'],
            ['name' => 'SC Braga', 'league' => 'Primeira Liga'],

            // ── Liga Argentina ──
            ['name' => 'River Plate', 'league' => 'Liga Argentina'],
            ['name' => 'Boca Juniors', 'league' => 'Liga Argentina'],
            ['name' => 'Racing Club', 'league' => 'Liga Argentina'],
            ['name' => 'Independiente', 'league' => 'Liga Argentina'],
            ['name' => 'San Lorenzo', 'league' => 'Liga Argentina'],
            ['name' => 'Estudiantes LP', 'league' => 'Liga Argentina'],

            // ── Liga Brasileira ──
            ['name' => 'Flamengo', 'league' => 'Liga Brasileira'],
            ['name' => 'Palmeiras', 'league' => 'Liga Brasileira'],
            ['name' => 'Corinthians', 'league' => 'Liga Brasileira'],
            ['name' => 'Sao Paulo FC', 'league' => 'Liga Brasileira'],
            ['name' => 'Santos FC', 'league' => 'Liga Brasileira'],
            ['name' => 'Gremio', 'league' => 'Liga Brasileira'],
            ['name' => 'Internacional', 'league' => 'Liga Brasileira'],
            ['name' => 'Atletico Mineiro', 'league' => 'Liga Brasileira'],

            // ── Liga MX ──
            ['name' => 'Club America', 'league' => 'Liga MX'],
            ['name' => 'Cruz Azul', 'league' => 'Liga MX'],
            ['name' => 'Chivas Guadalajara', 'league' => 'Liga MX'],
            ['name' => 'Tigres UANL', 'league' => 'Liga MX'],
            ['name' => 'Monterrey', 'league' => 'Liga MX'],

            // ── Saudi Pro League ──
            ['name' => 'Al Hilal', 'league' => 'Saudi Pro League'],
            ['name' => 'Al Nassr', 'league' => 'Saudi Pro League'],
            ['name' => 'Al Ahli', 'league' => 'Saudi Pro League'],
            ['name' => 'Al Ittihad', 'league' => 'Saudi Pro League'],

            // ── National Teams ──
            ['name' => 'Argentina', 'league' => 'Tim Nasional'],
            ['name' => 'Brazil', 'league' => 'Tim Nasional'],
            ['name' => 'France', 'league' => 'Tim Nasional'],
            ['name' => 'England', 'league' => 'Tim Nasional'],
            ['name' => 'Spain', 'league' => 'Tim Nasional'],
            ['name' => 'Germany', 'league' => 'Tim Nasional'],
            ['name' => 'Italy', 'league' => 'Tim Nasional'],
            ['name' => 'Portugal', 'league' => 'Tim Nasional'],
            ['name' => 'Netherlands', 'league' => 'Tim Nasional'],
            ['name' => 'Japan', 'league' => 'Tim Nasional'],
            ['name' => 'South Korea', 'league' => 'Tim Nasional'],
            ['name' => 'Morocco', 'league' => 'Tim Nasional'],
            ['name' => 'Colombia', 'league' => 'Tim Nasional'],
            ['name' => 'Uruguay', 'league' => 'Tim Nasional'],
            ['name' => 'Croatia', 'league' => 'Tim Nasional'],
        ];

        foreach ($clubs as $club) {
            \App\Models\Club::firstOrCreate(['name' => $club['name']], $club);
        }

        // Seed PlayStation Units
        $units = [
            ['code' => 'PS-01', 'name' => 'Console PlayStation 01', 'location' => 'TV 1', 'console_type' => 'PS4', 'controller_count' => 2, 'status' => 'active'],
            ['code' => 'PS-02', 'name' => 'Console PlayStation 02', 'location' => 'TV 2', 'console_type' => 'PS4', 'controller_count' => 2, 'status' => 'active'],
            ['code' => 'PS-03', 'name' => 'Console PlayStation 03', 'location' => 'TV 3', 'console_type' => 'PS3', 'controller_count' => 2, 'status' => 'active'],
            ['code' => 'PS-04', 'name' => 'Console PlayStation 04', 'location' => 'TV 4', 'console_type' => 'PS5', 'controller_count' => 2, 'status' => 'active'],
        ];

        foreach ($units as $unit) {
            \App\Models\PsUnit::firstOrCreate(['code' => $unit['code']], $unit);
        }

        // 3. Call ShieldSeeder to generate roles and permissions
        $this->call(ShieldSeeder::class);

        // 4. Create default Super Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@boxzone.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password123'),
            ]
        );

        // Assign super_admin role to the created admin user
        $admin->assignRole('super_admin');
    }
}
