<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::create([
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $seller = User::create([
                'name' => 'Main Seller',
                'email' => 'seller@example.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'is_active' => true,
            ]);

            $categories = [];
            foreach ([
                ['name' => 'Tablets', 'description' => 'Tablet medicines'],
                ['name' => 'Capsules', 'description' => 'Capsule medicines'],
                ['name' => 'Syrups', 'description' => 'Liquid oral medicines'],
                ['name' => 'Injections', 'description' => 'Injectable medicines'],
                ['name' => 'Creams', 'description' => 'Topical medicines'],
            ] as $data) {
                $categories[$data['name']] = Category::create($data + ['is_active' => true]);
            }

            $manufacturers = [];
            foreach ([
                ['name' => 'Demo Pharma Ltd.', 'phone' => '0300-1111111', 'email' => 'info@demopharma.test', 'address' => 'Islamabad'],
                ['name' => 'HealthCare Laboratories', 'phone' => '0300-2222222', 'email' => 'info@healthcare.test', 'address' => 'Lahore'],
                ['name' => 'National Medicines Co.', 'phone' => '0300-3333333', 'email' => 'info@nationalmed.test', 'address' => 'Karachi'],
            ] as $data) {
                $manufacturers[$data['name']] = Manufacturer::create($data + ['is_active' => true]);
            }

            $medicineData = [
                [
                    'product_code' => 'MED-001', 'barcode' => '890100000001', 'name' => 'Panadol 500mg',
                    'generic_name' => 'Paracetamol', 'category_id' => $categories['Tablets']->id,
                    'manufacturer_id' => $manufacturers['Demo Pharma Ltd.']->id, 'dosage_form' => 'Tablet',
                    'strength' => '500mg', 'pack_size' => '20 tablets', 'unit' => 'Pack',
                    'default_sale_price' => 120, 'mrp' => 130, 'reorder_level' => 20, 'is_active' => true,
                ],
                [
                    'product_code' => 'MED-002', 'barcode' => '890100000002', 'name' => 'Amoxil 500mg',
                    'generic_name' => 'Amoxicillin', 'category_id' => $categories['Capsules']->id,
                    'manufacturer_id' => $manufacturers['HealthCare Laboratories']->id, 'dosage_form' => 'Capsule',
                    'strength' => '500mg', 'pack_size' => '10 capsules', 'unit' => 'Pack',
                    'default_sale_price' => 220, 'mrp' => 240, 'reorder_level' => 15, 'is_active' => true,
                ],
                [
                    'product_code' => 'MED-003', 'barcode' => '890100000003', 'name' => 'Brufen 400mg',
                    'generic_name' => 'Ibuprofen', 'category_id' => $categories['Tablets']->id,
                    'manufacturer_id' => $manufacturers['National Medicines Co.']->id, 'dosage_form' => 'Tablet',
                    'strength' => '400mg', 'pack_size' => '20 tablets', 'unit' => 'Pack',
                    'default_sale_price' => 180, 'mrp' => 200, 'reorder_level' => 20, 'is_active' => true,
                ],
                [
                    'product_code' => 'MED-004', 'barcode' => '890100000004', 'name' => 'Calpol Syrup',
                    'generic_name' => 'Paracetamol', 'category_id' => $categories['Syrups']->id,
                    'manufacturer_id' => $manufacturers['Demo Pharma Ltd.']->id, 'dosage_form' => 'Syrup',
                    'strength' => '120mg/5ml', 'pack_size' => '60ml', 'unit' => 'Bottle',
                    'default_sale_price' => 95, 'mrp' => 110, 'reorder_level' => 10, 'is_active' => true,
                ],
                [
                    'product_code' => 'MED-005', 'barcode' => '890100000005', 'name' => 'Diclofenac Injection',
                    'generic_name' => 'Diclofenac Sodium', 'category_id' => $categories['Injections']->id,
                    'manufacturer_id' => $manufacturers['National Medicines Co.']->id, 'dosage_form' => 'Injection',
                    'strength' => '75mg/3ml', 'pack_size' => '5 ampoules', 'unit' => 'Box',
                    'default_sale_price' => 260, 'mrp' => 290, 'reorder_level' => 10, 'is_active' => true,
                ],
                [
                    'product_code' => 'MED-006', 'barcode' => '890100000006', 'name' => 'Clotrimazole Cream',
                    'generic_name' => 'Clotrimazole', 'category_id' => $categories['Creams']->id,
                    'manufacturer_id' => $manufacturers['HealthCare Laboratories']->id, 'dosage_form' => 'Cream',
                    'strength' => '1%', 'pack_size' => '20g', 'unit' => 'Tube',
                    'default_sale_price' => 150, 'mrp' => 170, 'reorder_level' => 10, 'is_active' => true,
                ],
            ];

            $medicines = [];
            foreach ($medicineData as $data) {
                $medicines[$data['product_code']] = Medicine::create($data);
            }

            $batches = [];
            foreach ([
                ['code' => 'MED-001', 'batch_no' => 'PANA-2601', 'purchase_price' => 90, 'sale_price' => 120, 'mrp' => 130, 'quantity' => 100, 'expiry_date' => '2028-12-31'],
                ['code' => 'MED-001', 'batch_no' => 'PANA-2602', 'purchase_price' => 92, 'sale_price' => 122, 'mrp' => 130, 'quantity' => 80, 'expiry_date' => '2029-06-30'],
                ['code' => 'MED-002', 'batch_no' => 'AMOX-2601', 'purchase_price' => 170, 'sale_price' => 220, 'mrp' => 240, 'quantity' => 60, 'expiry_date' => '2028-10-31'],
                ['code' => 'MED-003', 'batch_no' => 'BRUF-2601', 'purchase_price' => 135, 'sale_price' => 180, 'mrp' => 200, 'quantity' => 75, 'expiry_date' => '2028-08-31'],
                ['code' => 'MED-004', 'batch_no' => 'CALP-2601', 'purchase_price' => 70, 'sale_price' => 95, 'mrp' => 110, 'quantity' => 50, 'expiry_date' => '2027-12-31'],
                ['code' => 'MED-005', 'batch_no' => 'DICL-2601', 'purchase_price' => 200, 'sale_price' => 260, 'mrp' => 290, 'quantity' => 40, 'expiry_date' => '2027-11-30'],
                ['code' => 'MED-006', 'batch_no' => 'CLOT-2601', 'purchase_price' => 110, 'sale_price' => 150, 'mrp' => 170, 'quantity' => 45, 'expiry_date' => '2028-05-31'],
            ] as $data) {
                $batches[$data['batch_no']] = Batch::create([
                    'medicine_id' => $medicines[$data['code']]->id,
                    'batch_no' => $data['batch_no'],
                    'manufacturing_date' => '2026-01-15',
                    'expiry_date' => $data['expiry_date'],
                    'purchase_price' => $data['purchase_price'],
                    'sale_price' => $data['sale_price'],
                    'mrp' => $data['mrp'],
                    'quantity' => $data['quantity'],
                    'status' => 'Available',
                ]);
            }

            $customers = [];
            foreach ([
                ['customer_code' => 'CUS-001', 'business_name' => 'City Medical Store', 'contact_person' => 'Ahmed Khan', 'phone' => '0300-4444444', 'email' => 'city@example.com', 'address' => 'Swabi', 'license_no' => 'LIC-001', 'credit_limit' => 100000, 'opening_balance' => 0],
                ['customer_code' => 'CUS-002', 'business_name' => 'Al-Shifa Pharmacy', 'contact_person' => 'Bilal Ahmad', 'phone' => '0300-5555555', 'email' => 'alshifa@example.com', 'address' => 'Mardan', 'license_no' => 'LIC-002', 'credit_limit' => 50000, 'opening_balance' => 0],
                ['customer_code' => 'CUS-003', 'business_name' => 'Health Plus Medical', 'contact_person' => 'Usman Ali', 'phone' => '0300-6666666', 'email' => 'healthplus@example.com', 'address' => 'Peshawar', 'license_no' => 'LIC-003', 'credit_limit' => 75000, 'opening_balance' => 0],
            ] as $data) {
                $customers[$data['customer_code']] = Customer::create($data + ['is_active' => true, 'deleted' => false]);
            }

            // One sample sale so the Sales module can be tested immediately.
            $sampleBatch = $batches['PANA-2601'];
            $quantity = 2;
            $unitPrice = 120;
            $lineDiscount = 0;
            $subtotal = $quantity * $unitPrice;
            $invoiceDiscountPercent = 10;
            $invoiceDiscountAmount = round($subtotal * ($invoiceDiscountPercent / 100), 2);
            $grandTotal = round($subtotal - $invoiceDiscountAmount, 2);
            $paid = 100;

            $sampleSale = Sale::create([
                'invoice_no' => 'SAL-SEED-001',
                'customer_id' => $customers['CUS-001']->id,
                'sale_date' => now()->toDateString(),
                'payment_status' => 'Partial',
                'subtotal' => $subtotal,
                'discount' => $lineDiscount + $invoiceDiscountAmount,
                'invoice_discount' => $invoiceDiscountPercent,
                'tax' => 0,
                'grand_total' => $grandTotal,
                'paid_amount' => $paid,
                'status' => 'Completed',
                'created_by' => $seller->id,
            ]);

            SaleItem::create([
                'sale_id' => $sampleSale->id,
                'medicine_id' => $sampleBatch->medicine_id,
                'batch_id' => $sampleBatch->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $lineDiscount,
                'tax' => 0,
                'total' => $subtotal,
            ]);

            $sampleBatch->decrement('quantity', $quantity);
        });
    }
}
