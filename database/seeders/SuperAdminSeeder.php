<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $username = 'Brauliovh3';
        $password = '1Leucemia1'; // will be hashed

        $user = User::updateOrCreate(
            ['username' => $username],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make($password),
                'role' => 'superadmin',
                'status' => 'approved',
                'approved_at' => Carbon::now(),
            ]
        );

        // Ensure approved status and role
        $user->role = 'superadmin';
        $user->status = 'approved';
        $user->approved_at = $user->approved_at ?? Carbon::now();
        $user->save();

        $this->command->info("Superadmin user ensured: {$username}");
    }
}
