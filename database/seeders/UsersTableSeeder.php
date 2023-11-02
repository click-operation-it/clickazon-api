<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Interfaces\UserStatusInterface;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customerRole = config('roles.models.role')::where('name', '=', 'Customer')->first();
        $adminRole = config('roles.models.role')::where('name', '=', 'Admin')->first();
        $merchantRole = config('roles.models.role')::where('name', '=', 'Merchant')->first();
        $permissions = config('roles.models.permission')::all();

        /*
         * Add Users
         *
         */
        if (User::where('email', '=', 'admin@clickazon.com')->first() === null) {
            $newUser = User::create([
                'firstname'     => 'Admin',
                'lastname'     => 'Clickazon',
                'email'    => 'admin@clickazon.com',
                'phoneno' => '9088112266',
                'is_verified' => true,
                'is_active' => UserStatusInterface::ACTIVE,
                'can_login' => true,
                'is_completed' => true,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);

            $newUser->attachRole($adminRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        if (User::where('email', '=', 'merchant@clickazon.com')->first() === null) {
            $newUser = User::create([
                'firstname'     => 'Merchant',
                'lastname'     => 'Clickazon',
                'email'    => 'merchant@clickazon.com',
                'phoneno' => '9088112255',
                'is_verified' => true,
                'is_active' => UserStatusInterface::ACTIVE,
                'can_login' => true,
                'is_completed' => true,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);

            $newUser->attachRole($merchantRole);
        }

        if (User::where('email', '=', 'customer@clickazon.com')->first() === null) {
            $newUser = User::create([
                'firstname'     => 'Customer',
                'lastname'     => 'Clickazon',
                'email'    => 'customer@clickazon.com',
                'phoneno' => '9088351255',
                'is_verified' => true,
                'is_active' => UserStatusInterface::ACTIVE,
                'can_login' => true,
                'is_completed' => true,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);

            $newUser->attachRole($customerRole);
        }
        
    }
}
