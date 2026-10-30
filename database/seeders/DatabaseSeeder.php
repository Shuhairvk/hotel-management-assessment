<?php
namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@appcom.com'], [
            'name' => 'Admin User', 'password' => 'password123', 'role' => 'admin', 'is_active' => true,
        ]);
        foreach ([['101', 'Standard', 150], ['205', 'Deluxe', 225], ['402', 'Suite', 300]] as [$n, $t, $p]) {
            Room::firstOrCreate(['room_number' => $n], ['type' => $t, 'price_per_night' => $p]);
        }
    }
}
