<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\ProductUnit;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ProductSupplier::truncate();
        ProductUnit::truncate();
        Inventory::truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $catRauCu = Category::where('code', 'DM-RCQ')->first()?->id ?? 1;
        $catKho = Category::where('code', 'DM-TPK')->first()?->id ?? 2;
        $catGiaVi = Category::where('code', 'DM-GV')->first()?->id ?? 3;
        $catDoUong = Category::where('code', 'DM-DU')->first()?->id ?? 4;
        $catHoaPham = Category::where('code', 'DM-HP')->first()?->id ?? 5;

        $unitCai = Unit::where('code', 'DVT-001')->first()?->id ?? 1;
        $unitChai = Unit::where('code', 'DVT-002')->first()?->id ?? 2;
        $unitLon = Unit::where('code', 'DVT-003')->first()?->id ?? 3;
        $unitHop = Unit::where('code', 'DVT-004')->first()?->id ?? 4;
        $unitGoi = Unit::where('code', 'DVT-005')->first()?->id ?? 5;
        $unitThung = Unit::where('code', 'DVT-006')->first()?->id ?? 6;
        $unitKg = Unit::where('code', 'DVT-007')->first()?->id ?? 7;
        $unitBo = Unit::where('code', 'DVT-011')->first()?->id ?? 11;
        $unitTui = Unit::where('code', 'DVT-013')->first()?->id ?? 13;
        $unitVi = Unit::where('code', 'DVT-014')->first()?->id ?? 14;
        $unitLoc = Unit::where('code', 'DVT-015')->first()?->id ?? 15;

        $supDaLat = Supplier::where('code', 'NCC-001')->first()?->id ?? 1;
        $supVinamilk = Supplier::where('code', 'NCC-002')->first()?->id ?? 2;
        $supPepsi = Supplier::where('code', 'NCC-003')->first()?->id ?? 3;
        $supGao = Supplier::where('code', 'NCC-005')->first()?->id ?? 5;

        $productsData = [
            [
                'code' => 'SP-0001',
                'name' => 'Gạo thơm ST25 Sóc Trăng Thượng Hạng túi 5kg',
                'category_id' => $catKho,
                'description' => 'Gạo ngon nhất thế giới, hạt dài, thơm dẻo đậm đà chuẩn vụ thu đông.',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitTui,
                        'barcode' => '8936014350012',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 145000,
                        'selling_price' => 185000,
                    ],
                ],
                'suppliers' => [
                    [
                        'supplier_id' => $supGao,
                        'supplier_product_code' => 'ST25-5KG',
                        'purchase_price' => 145000,
                        'is_default' => true,
                    ]
                ],
                'inventory' => [
                    'quantity' => 45,
                    'min_stock' => 10,
                    'cost_price' => 145000,
                ],
            ],
            [
                'code' => 'SP-0002',
                'name' => 'Nước ngọt Pepsi lon 320ml',
                'category_id' => $catDoUong,
                'description' => 'Vị ngọt thanh sảng khoái và đã khát với hạt ga bùng nổ.',
                'image' => 'https://images.unsplash.com/photo-1629203851122-3726ecdf080e?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitLon,
                        'barcode' => '8934588012111',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 8200,
                        'selling_price' => 10500,
                    ],
                    [
                        'unit_id' => $unitLoc,
                        'barcode' => '8934588012116',
                        'conversion_rate' => 6,
                        'is_base_unit' => false,
                        'cost_price' => 49000,
                        'selling_price' => 60000,
                    ],
                    [
                        'unit_id' => $unitThung,
                        'barcode' => '8934588012124',
                        'conversion_rate' => 24,
                        'is_base_unit' => false,
                        'cost_price' => 195000,
                        'selling_price' => 235000,
                    ],
                ],
                'suppliers' => [
                    [
                        'supplier_id' => $supPepsi,
                        'supplier_product_code' => 'PEP-320',
                        'purchase_price' => 195000,
                        'is_default' => true,
                    ]
                ],
                'inventory' => [
                    'quantity' => 240,
                    'min_stock' => 48,
                    'cost_price' => 8200,
                ],
            ],
            [
                'code' => 'SP-0003',
                'name' => 'Mì Hảo Hảo tôm chua cay gói 75g',
                'category_id' => $catKho,
                'description' => 'Sợi mì vàng dai giòn hòa quyện súp tôm chua cay đậm đà quen thuộc.',
                'image' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitGoi,
                        'barcode' => '8934563248160',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 3800,
                        'selling_price' => 4800,
                    ],
                    [
                        'unit_id' => $unitThung,
                        'barcode' => '8934563248130',
                        'conversion_rate' => 30,
                        'is_base_unit' => false,
                        'cost_price' => 110000,
                        'selling_price' => 138000,
                    ],
                ],
                'suppliers' => [],
                'inventory' => [
                    'quantity' => 350,
                    'min_stock' => 60,
                    'cost_price' => 3800,
                ],
            ],
            [
                'code' => 'SP-0004',
                'name' => 'Sữa tươi tiệt trùng Vinamilk 100% Có Đường hộp 180ml',
                'category_id' => $catDoUong,
                'description' => 'Làm từ 100% sữa bò tươi nguyên chất, dồi dào canxi và vitamin D3.',
                'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitHop,
                        'barcode' => '8934673121015',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 7400,
                        'selling_price' => 9200,
                    ],
                    [
                        'unit_id' => $unitLoc,
                        'barcode' => '8934673121046',
                        'conversion_rate' => 4,
                        'is_base_unit' => false,
                        'cost_price' => 29500,
                        'selling_price' => 36000,
                    ],
                    [
                        'unit_id' => $unitThung,
                        'barcode' => '8934673121480',
                        'conversion_rate' => 48,
                        'is_base_unit' => false,
                        'cost_price' => 345000,
                        'selling_price' => 415000,
                    ],
                ],
                'suppliers' => [
                    [
                        'supplier_id' => $supVinamilk,
                        'supplier_product_code' => 'VNM-CD-180',
                        'purchase_price' => 345000,
                        'is_default' => true,
                    ]
                ],
                'inventory' => [
                    'quantity' => 192,
                    'min_stock' => 48,
                    'cost_price' => 7400,
                ],
            ],
            [
                'code' => 'SP-0005',
                'name' => 'Cà chua Beef Đà Lạt sạch chuẩn VietGAP',
                'category_id' => $catRauCu,
                'description' => 'Trái to tròn mọng nước, cơm dày ít hạt, ngọt thơm dùng cho salad, nấu canh.',
                'image' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitKg,
                        'barcode' => '8938001001011',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 22000,
                        'selling_price' => 32000,
                    ],
                ],
                'suppliers' => [
                    [
                        'supplier_id' => $supDaLat,
                        'supplier_product_code' => 'DL-TOMATO',
                        'purchase_price' => 22000,
                        'is_default' => true,
                    ]
                ],
                'inventory' => [
                    'quantity' => 28,
                    'min_stock' => 10,
                    'cost_price' => 22000,
                ],
            ],
            [
                'code' => 'SP-0006',
                'name' => 'Xà lách Lô Lô Xanh Đà Lạt túi 500g',
                'category_id' => $catRauCu,
                'description' => 'Lá xoăn mềm ngọt mát, trồng theo phương pháp thủy canh chuẩn tươi sạch.',
                'image' => 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitTui,
                        'barcode' => '8938001002018',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 18000,
                        'selling_price' => 26000,
                    ],
                ],
                'suppliers' => [
                    [
                        'supplier_id' => $supDaLat,
                        'supplier_product_code' => 'DL-SALAD',
                        'purchase_price' => 18000,
                        'is_default' => true,
                    ]
                ],
                'inventory' => [
                    'quantity' => 14,
                    'min_stock' => 8,
                    'cost_price' => 18000,
                ],
            ],
            [
                'code' => 'SP-0007',
                'name' => 'Nước rửa chén Sunlight Chanh túi 2.1kg',
                'category_id' => $catHoaPham,
                'description' => 'Đánh bay dầu mỡ cứng đầu nhanh gấp 5 lần, khử sạch mùi tanh khó chịu.',
                'image' => 'https://images.unsplash.com/photo-1585670270608-b4be4fb880ed?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitTui,
                        'barcode' => '8934568102319',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 64000,
                        'selling_price' => 79000,
                    ],
                ],
                'suppliers' => [],
                'inventory' => [
                    'quantity' => 3,
                    'min_stock' => 8,
                    'cost_price' => 64000,
                ],
            ],
            [
                'code' => 'SP-0008',
                'name' => 'Bột ngọt Ajinomoto gói 454g',
                'category_id' => $catGiaVi,
                'description' => 'Gia vị umami chuẩn vị Nhật Bản lên men tự nhiên từ mật mía đường.',
                'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=600&auto=format&fit=crop&q=80',
                'units' => [
                    [
                        'unit_id' => $unitGoi,
                        'barcode' => '8934567210191',
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => 31000,
                        'selling_price' => 38000,
                    ],
                ],
                'suppliers' => [],
                'inventory' => [
                    'quantity' => 0,
                    'min_stock' => 15,
                    'cost_price' => 31000,
                ],
            ],
        ];

        foreach ($productsData as $item) {
            $units = $item['units'] ?? [];
            $suppliers = $item['suppliers'] ?? [];
            $inv = $item['inventory'] ?? [];

            unset($item['units'], $item['suppliers'], $item['inventory']);

            $product = Product::create($item);

            foreach ($units as $u) {
                $u['product_id'] = $product->id;
                ProductUnit::create($u);
            }

            foreach ($suppliers as $s) {
                $s['product_id'] = $product->id;
                ProductSupplier::create($s);
            }

            if (!empty($inv)) {
                $inv['product_id'] = $product->id;
                Inventory::create($inv);
            }
        }
    }
}
