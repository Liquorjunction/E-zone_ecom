<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StoreAndWarehouseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Stores
        $stores = [
            [
                'store_name' => ['en' => 'Main Store - Riyadh', 'ar' => 'المتجر الرئيسي - الرياض'],
                'store_id' => 'STORE-001',
                'store_footer' => ['en' => 'Your trusted electronics partner', 'ar' => 'شريكك الموثوق في الإلكترونيات'],
                'status' => 'active',
            ],
            [
                'store_name' => ['en' => 'Jeddah Branch', 'ar' => 'فرع جدة'],
                'store_id' => 'STORE-002',
                'store_footer' => ['en' => 'Quality electronics in Western Region', 'ar' => 'إلكترونيات عالية الجودة في المنطقة الغربية'],
                'status' => 'active',
            ],
            [
                'store_name' => ['en' => 'Dammam Store', 'ar' => 'متجر الدمام'],
                'store_id' => 'STORE-003',
                'store_footer' => ['en' => 'Electronics hub of Eastern Province', 'ar' => 'مركز الإلكترونيات في المنطقة الشرقية'],
                'status' => 'active',
            ],
        ];

        foreach ($stores as $store) {
            Store::firstOrCreate(
                ['store_id' => $store['store_id']],
                $store
            );
        }

        // Seed Warehouses
        $warehouses = [
            [
                'warehouse_name' => ['en' => 'Central Warehouse - Riyadh', 'ar' => 'المستودع المركزي - الرياض'],
                'warehouse_id' => 'WH-001',
                'location' => ['en' => 'Industrial Area, Riyadh, Saudi Arabia', 'ar' => 'المنطقة الصناعية، الرياض، المملكة العربية السعودية'],
                'status' => 'active',
            ],
            [
                'warehouse_name' => ['en' => 'Western Warehouse - Jeddah', 'ar' => 'المستودع الغربي - جدة'],
                'warehouse_id' => 'WH-002',
                'location' => ['en' => 'Industrial City, Jeddah, Saudi Arabia', 'ar' => 'المدينة الصناعية، جدة، المملكة العربية السعودية'],
                'status' => 'active',
            ],
            [
                'warehouse_name' => ['en' => 'Eastern Warehouse - Dammam', 'ar' => 'المستودع الشرقي - الدمام'],
                'warehouse_id' => 'WH-003',
                'location' => ['en' => 'Second Industrial City, Dammam, Saudi Arabia', 'ar' => 'المدينة الصناعية الثانية، الدمام، المملكة العربية السعودية'],
                'status' => 'active',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(
                ['warehouse_id' => $warehouse['warehouse_id']],
                $warehouse
            );
        }

        $this->command->info('Stores and warehouses seeded successfully!');
    }
}
