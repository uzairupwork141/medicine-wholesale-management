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
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::create(['name'=>'System Admin','email'=>'admin@example.com','password'=>Hash::make('password'),'role'=>'admin','is_active'=>true]);
            User::create(['name'=>'Sales User','email'=>'seller@example.com','password'=>Hash::make('password'),'role'=>'seller','is_active'=>true]);

            $cat1 = Category::create(['name'=>'Pain Relief','description'=>'Pain and fever medicines','is_active'=>true]);
            $cat2 = Category::create(['name'=>'Antibiotics','description'=>'Prescription antibiotics','is_active'=>true]);
            $cat3 = Category::create(['name'=>'Gastro','description'=>'Digestive medicines','is_active'=>true]);

            $man1 = Manufacturer::create(['name'=>'Getz Pharma','phone'=>'0911111111','email'=>'info@getz.example','address'=>'Pakistan','is_active'=>true]);
            $man2 = Manufacturer::create(['name'=>'Searle Pakistan','phone'=>'0922222222','email'=>'info@searle.example','address'=>'Pakistan','is_active'=>true]);
            $man3 = Manufacturer::create(['name'=>'High-Q Pharma','phone'=>'0933333333','email'=>null,'address'=>'Pakistan','is_active'=>true]);

            $m1 = Medicine::create(['product_code'=>'MED-001','barcode'=>'890100000001','name'=>'Paracetamol 500mg','generic_name'=>'Paracetamol','category_id'=>$cat1->id,'manufacturer_id'=>$man1->id,'dosage_form'=>'Tablet','strength'=>'500mg','pack_size'=>'20 tablets','unit'=>'Box','is_active'=>true]);
            $m2 = Medicine::create(['product_code'=>'MED-002','barcode'=>'890100000002','name'=>'Amoxicillin 500mg','generic_name'=>'Amoxicillin','category_id'=>$cat2->id,'manufacturer_id'=>$man2->id,'dosage_form'=>'Capsule','strength'=>'500mg','pack_size'=>'20 capsules','unit'=>'Box','is_active'=>true]);
            $m3 = Medicine::create(['product_code'=>'MED-003','barcode'=>'890100000003','name'=>'Omeprazole 20mg','generic_name'=>'Omeprazole','category_id'=>$cat3->id,'manufacturer_id'=>$man3->id,'dosage_form'=>'Capsule','strength'=>'20mg','pack_size'=>'14 capsules','unit'=>'Box','is_active'=>true]);
            $m4 = Medicine::create(['product_code'=>'MED-004','barcode'=>'890100000004','name'=>'Ibuprofen 400mg','generic_name'=>'Ibuprofen','category_id'=>$cat1->id,'manufacturer_id'=>$man1->id,'dosage_form'=>'Tablet','strength'=>'400mg','pack_size'=>'20 tablets','unit'=>'Box','is_active'=>true]);

            $b1 = Batch::create(['medicine_id'=>$m1->id,'batch_no'=>'PCM-SEP26','manufacturing_date'=>'2026-01-10','expiry_date'=>'2028-01-10','purchase_price'=>80,'sale_price'=>100,'mrp'=>120,'quantity'=>500,'status'=>'Available']);
            $b2 = Batch::create(['medicine_id'=>$m2->id,'batch_no'=>'AMX-SEP26','manufacturing_date'=>'2026-02-01','expiry_date'=>'2028-02-01','purchase_price'=>150,'sale_price'=>190,'mrp'=>220,'quantity'=>300,'status'=>'Available']);
            $b3 = Batch::create(['medicine_id'=>$m3->id,'batch_no'=>'OMP-SEP26','manufacturing_date'=>'2026-03-05','expiry_date'=>'2028-03-05','purchase_price'=>90,'sale_price'=>120,'mrp'=>140,'quantity'=>300,'status'=>'Available']);
            $b4 = Batch::create(['medicine_id'=>$m4->id,'batch_no'=>'IBU-SEP26','manufacturing_date'=>'2026-04-01','expiry_date'=>'2028-04-01','purchase_price'=>70,'sale_price'=>95,'mrp'=>110,'quantity'=>300,'status'=>'Available']);

            $c1 = Customer::create(['customer_code'=>'CUS-001','business_name'=>'ABC Medical Store','contact_person'=>'Ahmad Khan','phone'=>'03001234567','email'=>'abc@example.com','address'=>'Swabi','license_no'=>'LIC-001','is_active'=>true,'deleted'=>false]);
            $c2 = Customer::create(['customer_code'=>'CUS-002','business_name'=>'City Pharmacy','contact_person'=>'Bilal Ahmad','phone'=>'03111234567','email'=>'city@example.com','address'=>'Mardan','license_no'=>'LIC-002','is_active'=>true,'deleted'=>false]);
            Customer::create(['customer_code'=>'CUS-003','business_name'=>'Health Care Pharmacy','contact_person'=>'Usman Ali','phone'=>'03221234567','email'=>null,'address'=>'Peshawar','license_no'=>null,'is_active'=>true,'deleted'=>false]);

            $makeSale = function(Customer $customer, Batch $batch, float $total, float $paid, int $daysAgo=0, float $invoicePct=0, ?float $unitPrice=null) use ($admin) {
                $unitPrice = $unitPrice ?? (float)$batch->sale_price;
                $qty = (int)round($total / $unitPrice);
                $gross = round($qty * $unitPrice, 2);
                $grand = $gross;
                $due = round($grand - $paid, 2);
                $status = $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Pending');
                if ($qty > $batch->quantity) throw new \RuntimeException('Seed stock is insufficient.');
                $batch->decrement('quantity',$qty);
                $sale = Sale::create([
                    'invoice_no'=>'SAL-'.now()->subDays($daysAgo)->format('Ymd').'-'.Str::upper(Str::random(6)),
                    'customer_id'=>$customer->id,'sale_date'=>now()->subDays($daysAgo)->toDateString(),'payment_status'=>$status,
                    'subtotal'=>$gross,'discount'=>0,'invoice_discount'=>$invoicePct,'tax'=>0,'grand_total'=>$grand,'paid_amount'=>$paid,
                    'status'=>'Completed','created_by'=>$admin->id,
                ]);
                SaleItem::create(['sale_id'=>$sale->id,'medicine_id'=>$batch->medicine_id,'batch_id'=>$batch->id,'quantity'=>$qty,'unit_price'=>$unitPrice,'discount'=>0,'tax'=>0,'total'=>$gross]);
            };

            // Five historical sales for one customer: three have a combined Rs. 5,000 outstanding balance.
            $makeSale($c1,$b1,1000,1000,30);
            $makeSale($c1,$b2,1500,1500,24);
            $makeSale($c1,$b3,2000,0,18,0,100);
            $makeSale($c1,$b4,2000,0,10,0,100);
            $makeSale($c1,$b1,1000,0,3,0,100);

            $makeSale($c2,$b2,1900,900,7);
            $makeSale($c2,$b3,1200,1200,2);
        });
    }
}
