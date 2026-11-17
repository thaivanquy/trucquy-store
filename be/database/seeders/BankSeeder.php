<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $rawBanks = null;

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->get('https://api.vietqr.io/v2/banks');

            if ($response->successful()) {
                $json = $response->json();
                $rawBanks = $json['data'] ?? null;
            }
        } catch (\Throwable $e) {

        }

        if (empty($rawBanks)) {
            $localPath = database_path('data/vietqr_banks.json');
            if (File::exists($localPath)) {
                $json = json_decode(File::get($localPath), true);
                $rawBanks = $json['data'] ?? null;
            }
        }

        if (empty($rawBanks) || !is_array($rawBanks)) {
            return;
        }

        DB::transaction(function () use ($rawBanks) {
            $banks = [];
            $now = now();

            foreach ($rawBanks as $b) {
                $banks[] = [
                    'id' => (int) $b['id'],
                    'name' => $b['name'],
                    'code' => $b['code'] ?? null,
                    'bin' => $b['bin'] ?? null,
                    'short_name' => $b['short_name'] ?? $b['shortName'] ?? null,
                    'logo' => $b['logo'] ?? null,
                    'transfer_supported' => !empty($b['transferSupported']),
                    'lookup_supported' => !empty($b['lookupSupported']),
                    'support' => (int) ($b['support'] ?? 0),
                    'is_transfer' => !empty($b['isTransfer']),
                    'swift_code' => $b['swift_code'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            Bank::upsert(
                $banks,
                ['id'],
                ['name', 'code', 'bin', 'short_name', 'logo', 'transfer_supported', 'lookup_supported', 'support', 'is_transfer', 'swift_code', 'updated_at']
            );
        });
    }
}
