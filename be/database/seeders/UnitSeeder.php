<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'code' => 'DVT-001',
                'name' => 'Cái',
                'description' => 'Đơn vị đếm thông dụng cho các sản phẩm đơn lẻ (bàn chải, xà bông, đồ dùng gia đình...)',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-002',
                'name' => 'Chai',
                'description' => 'Đơn vị đóng chai thuỷ tinh hoặc nhựa (nước tương, dầu ăn, nước mắm, đồ uống...)',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-003',
                'name' => 'Lon',
                'description' => 'Đơn vị đóng lon kim loại (nước ngọt, bia, sữa đặc, đồ hộp...)',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-004',
                'name' => 'Hộp',
                'description' => 'Đơn vị hộp giấy hoặc nhựa (bánh quy, trà túi lọc, ngũ cốc, gia vị...)',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-005',
                'name' => 'Gói',
                'description' => 'Đơn vị đóng gói màng nhôm/nhựa (mì gói, bột canh, bánh snack, hạt giống...)',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-006',
                'name' => 'Thùng',
                'description' => 'Quy cách đóng thùng carton số lượng lớn cho bán buôn hoặc lốc lớn',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-007',
                'name' => 'Kg',
                'description' => 'Kilogram - Đơn vị đo khối lượng chuẩn cho rau, củ, quả, thịt tươi sống, gạo...',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-008',
                'name' => 'Gram',
                'description' => 'Gram (g) - Khối lượng nhỏ cho gia vị, tiêu, trà, thảo mộc khô',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-009',
                'name' => 'Lít',
                'description' => 'Lít (L) - Đơn vị thể tích chất lỏng',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-010',
                'name' => 'Ml',
                'description' => 'Milliliter (ml) - Thể tích nhỏ cho tinh dầu, siro, nước hoa...',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-011',
                'name' => 'Bó',
                'description' => 'Bó rau ăn lá (rau muống, mồng tơi, cải xoang, hành lá, hoa tươi...)',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-012',
                'name' => 'Trái',
                'description' => 'Trái / quả hoa quả bán lẻ (dưa hấu, bưởi da xanh, dừa xiêm, đu đủ...)',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-013',
                'name' => 'Túi',
                'description' => 'Túi nilon/giấy đóng sẵn (gạo túi 5kg, rau củ sơ chế, thực phẩm đông lạnh)',
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-014',
                'name' => 'Vỉ',
                'description' => 'Vỉ đóng (vỉ trứng 10 quả, vỉ sữa chua 4 hộp, vỉ pin, vỉ kẹo cao su...)',
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'code' => 'DVT-015',
                'name' => 'Lốc',
                'description' => 'Lốc 4-6 lon/chai hoặc gói bọc màng co nhiệt',
                'sort_order' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}
