<?php

namespace Database\Seeders;
use App\Models\Admin;  
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        if (!Admin::where('email', 'admin@example.com')->exists()) {
            Admin::create([
                'name' => 'Admin User', // Admin name
                'email' => 'admin@gmail.com', // Admin email
                'password' => Hash::make('admin123'), // Admin password, hashed for security
            ]);
        }
    }
}
