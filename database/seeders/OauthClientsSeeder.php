<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class OauthClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* START - Tạo Password Grant Client */
        $clientName = 'DoChoiWinWinPasswordGrantClient';
        $clientRepository = new ClientRepository();

        DB::table('oauth_clients')
            ->where('name', 'WinWinTraiCayPasswordGrantClient')
            ->update(['name' => $clientName]);

        // Kiểm tra client đã tồn tại chưa theo tên
        $existingClient = DB::table('oauth_clients')->where('name', $clientName)->first();

        if (!$existingClient) {
            $client = $clientRepository->createPasswordGrantClient(
                null, 
                $clientName, 
                'http://localhost/callback'
            );

            /* echo "Created Client ID: " . $client->id . PHP_EOL;
            echo "Created Client Secret: " . $client->secret . PHP_EOL; */
        } else {
            echo "Client '{$clientName}' already exists, skipping creation." . PHP_EOL;
        }
        /* END - Tạo Password Grant Client */


        /* START - Tạo Personal Access Client */
        $personalClientName = 'DoChoiWinWinPersonalAccessClient';
        DB::table('oauth_clients')
            ->where('name', 'WinWinTraiCayPersonalAccessClient')
            ->update(['name' => $personalClientName]);
        $existingPersonalClient = DB::table('oauth_clients')
            ->where('name', $personalClientName)
            ->where('personal_access_client', true)
            ->first();

        if (!$existingPersonalClient) {
            $clientRepository->createPersonalAccessClient(
                null,
                $personalClientName,
                'http://localhost'
            );
            /* echo "Created Personal Access Client: {$personalClientName}" . PHP_EOL; */
        } else {
            echo "Personal Access Client '{$personalClientName}' already exists, skipping." . PHP_EOL;
        }
        /* END - Tạo Personal Access Client */
    }
    
}
