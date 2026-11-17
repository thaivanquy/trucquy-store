<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AdministrativeDivisionSeeder extends Seeder
{
    public function run(): void
    {
        $data = null;

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->get('https://provinces.open-api.vn/api/v2/?depth=2');

            if ($response->successful()) {
                $data = $response->json();
            }
        } catch (\Throwable $e) {

        }

        if (empty($data)) {
            $localPath = database_path('data/provinces_v2.json');
            if (File::exists($localPath)) {
                $data = json_decode(File::get($localPath), true);
            }
        }

        if (empty($data) || !is_array($data)) {
            return;
        }

        DB::transaction(function () use ($data) {
            $provinces = [];
            $wards = [];
            $now = now();

            foreach ($data as $p) {
                $provinceCode = (int) $p['code'];
                $provinces[] = [
                    'code' => $provinceCode,
                    'name' => $p['name'],
                    'division_type' => $p['division_type'] ?? null,
                    'codename' => $p['codename'] ?? null,
                    'phone_code' => isset($p['phone_code']) ? (int) $p['phone_code'] : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (!empty($p['wards']) && is_array($p['wards'])) {
                    foreach ($p['wards'] as $w) {
                        $wards[] = [
                            'code' => (int) $w['code'],
                            'province_code' => $provinceCode,
                            'name' => $w['name'],
                            'division_type' => $w['division_type'] ?? null,
                            'codename' => $w['codename'] ?? null,
                            'short_codename' => $w['short_codename'] ?? null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            Province::upsert($provinces, ['code'], ['name', 'division_type', 'codename', 'phone_code', 'updated_at']);

            foreach (array_chunk($wards, 500) as $chunk) {
                Ward::upsert($chunk, ['code'], ['province_code', 'name', 'division_type', 'codename', 'short_codename', 'updated_at']);
            }
        });
    }
}
