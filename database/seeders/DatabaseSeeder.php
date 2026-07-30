<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account for logging in.
        User::updateOrCreate(
            ['email' => 'admin@gadiskreatif.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Company details shown on every document (editable in Settings).
        CompanySetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'GADIS KREATIF ENTERPRISE',
                'reg_no' => '202403213750 (NS0299725-V)',
                'address' => "TS 7097, MK 9, KG. TOK ELONG,\n14000 BUKIT MERTAJAM, PULAU PINANG.",
                'email' => 'gadiskreatif99@gmail.com',
                'phone' => '011-1443 5580 (Jue)',
                'bank_info' => 'GADIS KREATIF ENTERPRISE (557090805468 MBB)',
                'default_terms' => "1. Term: CASH/TRANSFER: This price is for this specification only. Any changes will be informed later.\n2. All cheques/payments are made payable to the account:",
                'next_number' => 26133,
            ]
        );
    }
}
