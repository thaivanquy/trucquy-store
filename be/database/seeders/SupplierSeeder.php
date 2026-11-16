<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'NCC-001',
                'name' => 'Công ty TNHH Rau Sạch Đà Lạt Farm',
                'phone' => '02633829911',
                'email' => 'contact@dalatfarm.vn',
                'address' => 'Số 12 Đường Hùng Vương, Phường 9',
                'province_id' => 68, // Lâm Đồng
                'ward_id' => null,
                'contact_person' => 'Nguyễn Văn Hùng',
                'contact_phone' => '0912345678',
                'bank_name' => 'Vietcombank (VCB)',
                'bank_account' => '0011004567890',
                'bank_account_name' => 'CTY TNHH RAU SACH DA LAT FARM',
                'opening_balance' => 15000000.00,
                'note' => 'Cung cấp rau củ quả tươi sạch hàng ngày lúc 5h sáng',
                'is_active' => true,
            ],
            [
                'code' => 'NCC-002',
                'name' => 'Công ty Cổ phần Sữa Việt Nam (Vinamilk)',
                'phone' => '02854155555',
                'email' => 'vinamilk@vinamilk.com.vn',
                'address' => 'Số 10 Tân Trào, Phường Tân Phú',
                'province_id' => 79, // TP. HCM
                'ward_id' => null,
                'contact_person' => 'Trần Thị Bích Ngọc',
                'contact_phone' => '0988776655',
                'bank_name' => 'BIDV',
                'bank_account' => '12010000987654',
                'bank_account_name' => 'CTY CP SUA VIET NAM',
                'opening_balance' => 8500000.00,
                'note' => 'Giao hàng thứ 3 và thứ 6 hàng tuần, công nợ gối đầu 30 ngày',
                'is_active' => true,
            ],
            [
                'code' => 'NCC-003',
                'name' => 'Công ty TNHH Nước giải khát Suntory PepsiCo VN',
                'phone' => '02838219318',
                'email' => 'orders@pepsico.com.vn',
                'address' => 'Tầng 5, Sheraton Saigon, 88 Đồng Khởi, Bến Nghé',
                'province_id' => 79, // TP. HCM
                'ward_id' => null,
                'contact_person' => 'Lê Hoàng Nam',
                'contact_phone' => '0903112233',
                'bank_name' => 'Techcombank (TCB)',
                'bank_account' => '1902888999888',
                'bank_account_name' => 'SUNTORY PEPSICO VN',
                'opening_balance' => 0.00,
                'note' => 'Chiết khấu 3% cho đơn hàng nước ngọt thanh toán ngay',
                'is_active' => true,
            ],
            [
                'code' => 'NCC-004',
                'name' => 'Tổng kho Phân phối Bánh kẹo Miền Nam',
                'phone' => '02839998888',
                'email' => 'kinhdoanh@banhkeomiennam.vn',
                'address' => 'Khu Công Nghiệp Tân Bình, Tây Thạnh',
                'province_id' => 79, // TP. HCM
                'ward_id' => null,
                'contact_person' => 'Phạm Quốc Cường',
                'contact_phone' => '0938445566',
                'bank_name' => 'MB Bank (MB)',
                'bank_account' => '888899990001',
                'bank_account_name' => 'PHAM QUOC CUONG',
                'opening_balance' => 5200000.00,
                'note' => 'Nhà phân phối sỉ bánh kẹo Kinh Đô, Orion, Bibica',
                'is_active' => true,
            ],
            [
                'code' => 'NCC-005',
                'name' => 'Hợp tác xã Nông sản Gạo Sạch Tháp Mười',
                'phone' => '02773882211',
                'email' => 'gaothapmuoi@dongthap.vn',
                'address' => 'Ấp Mỹ Thạnh, Xã Mỹ An Hưng',
                'province_id' => 87, // Đồng Tháp
                'ward_id' => null,
                'contact_person' => 'Võ Minh Trí',
                'contact_phone' => '0977889900',
                'bank_name' => 'Agribank',
                'bank_account' => '6900205123456',
                'bank_account_name' => 'HTX NONG SAN THAP MUOI',
                'opening_balance' => 20000000.00,
                'note' => 'Cung cấp gạo ST25, Đài Thơm 8 đóng túi 5kg và 10kg',
                'is_active' => false,
            ],
        ];

        foreach ($suppliers as $item) {
            Supplier::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
