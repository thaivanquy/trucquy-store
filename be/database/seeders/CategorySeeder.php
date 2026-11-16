<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            [
                'code' => 'DM-RCQ',
                'name' => 'Rau củ quả tươi',
                'slug' => 'rau-cu-qua-tuoi',
                'parent_id' => null,
                'description' => 'Các loại rau xanh, củ quả và nông sản tươi sạch thu hoạch trong ngày.',
                'image' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=400&auto=format&fit=crop&q=80',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'DM-TPK',
                'name' => 'Thực phẩm khô & Ngũ cốc',
                'slug' => 'thuc-pham-kho-va-ngu-coc',
                'parent_id' => null,
                'description' => 'Gạo, nếp, bún khô, miến, phở và các loại hạt nông sản.',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&auto=format&fit=crop&q=80',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'DM-GV',
                'name' => 'Gia vị & Dầu ăn',
                'slug' => 'gia-vi-va-dau-an',
                'parent_id' => null,
                'description' => 'Dầu thực vật, nước mắm, nước tương, hạt nêm, đường, muối.',
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&auto=format&fit=crop&q=80',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'DM-DU',
                'name' => 'Đồ uống & Giải khát',
                'slug' => 'do-uong-va-giai-khat',
                'parent_id' => null,
                'description' => 'Nước ngọt, nước khoáng tinh khiết, bia và trà đóng chai.',
                'image' => 'https://images.unsplash.com/photo-1527661591475-527312dd65f5?w=400&auto=format&fit=crop&q=80',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'DM-HP',
                'name' => 'Hóa phẩm & Tẩy rửa',
                'slug' => 'hoa-pham-va-tay-rua',
                'parent_id' => null,
                'description' => 'Nước giặt, nước rửa chén, nước lau sàn và chăm sóc gia đình.',
                'image' => 'https://images.unsplash.com/photo-1583947215259-38e31be8751f?w=400&auto=format&fit=crop&q=80',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'DM-BK',
                'name' => 'Bánh kẹo & Ăn vặt',
                'slug' => 'banh-keo-va-an-vat',
                'parent_id' => null,
                'description' => 'Bánh quy, kẹo dẻo, socola, khoai tây sấy và snack các loại.',
                'image' => 'https://images.unsplash.com/photo-1582293041079-7814c2f12063?w=400&auto=format&fit=crop&q=80',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        $rootMap = [];
        foreach ($roots as $data) {
            $cat = Category::updateOrCreate(['code' => $data['code']], $data);
            $rootMap[$data['code']] = $cat->id;
        }

        $children = [
            [
                'code' => 'DM-RCQ-01',
                'name' => 'Rau ăn lá',
                'slug' => 'rau-an-la',
                'parent_id' => $rootMap['DM-RCQ'] ?? null,
                'description' => 'Rau muống, cải ngọt, cải thìa, mồng tơi, xà lách, rau dền...',
                'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'DM-RCQ-02',
                'name' => 'Củ & Quả tươi',
                'slug' => 'cu-va-qua-tuoi',
                'parent_id' => $rootMap['DM-RCQ'] ?? null,
                'description' => 'Cà rốt, khoai tây, cà chua, dưa leo, bầu, bí đỏ...',
                'image' => 'https://images.unsplash.com/photo-1590779033100-9f60a05a013d?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'DM-TPK-01',
                'name' => 'Gạo & Nếp',
                'slug' => 'gao-va-nep',
                'parent_id' => $rootMap['DM-TPK'] ?? null,
                'description' => 'Gạo thơm ST25, gạo lài miên, nếp cái hoa vàng...',
                'image' => 'https://images.unsplash.com/photo-1536304929831-ee1ca9d44906?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'DM-TPK-02',
                'name' => 'Mì gói & Miến phở',
                'slug' => 'mi-goi-va-mien-pho',
                'parent_id' => $rootMap['DM-TPK'] ?? null,
                'description' => 'Mì Hảo Hảo, Omachi, phở Đệ Nhất, miến Phú Hương...',
                'image' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'DM-GV-01',
                'name' => 'Dầu ăn thực vật',
                'slug' => 'dau-an-thuc-vat',
                'parent_id' => $rootMap['DM-GV'] ?? null,
                'description' => 'Dầu ăn Simply, Neptune, Meizan, Cái Lân...',
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'DM-GV-02',
                'name' => 'Nước mắm & Nước tương',
                'slug' => 'nuoc-mam-va-nuoc-tuong',
                'parent_id' => $rootMap['DM-GV'] ?? null,
                'description' => 'Nước mắm Nam Ngư, Chinsu, nước tương Maggi...',
                'image' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'DM-DU-01',
                'name' => 'Nước ngọt & Nước khoáng',
                'slug' => 'nuoc-ngot-va-nuoc-khoang',
                'parent_id' => $rootMap['DM-DU'] ?? null,
                'description' => 'Coca-Cola, Pepsi, 7Up, trà C2, nước khoáng Aquafina...',
                'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'DM-DU-02',
                'name' => 'Bia & Nước có cồn',
                'slug' => 'bia-va-nuoc-co-con',
                'parent_id' => $rootMap['DM-DU'] ?? null,
                'description' => 'Bia Tiger, Heineken, Sài Gòn, bia 333...',
                'image' => 'https://images.unsplash.com/photo-1608270119238-65476a6cf49d?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'DM-HP-01',
                'name' => 'Giặt xả & Tẩy rửa',
                'slug' => 'giat-xa-va-tay-rua',
                'parent_id' => $rootMap['DM-HP'] ?? null,
                'description' => 'Nước giặt OMO, Comfort, Sunlight rửa chén, lau sàn...',
                'image' => 'https://images.unsplash.com/photo-1583947215259-38e31be8751f?w=300&auto=format&fit=crop&q=80',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($children as $child) {
            Category::updateOrCreate(['code' => $child['code']], $child);
        }
    }
}
