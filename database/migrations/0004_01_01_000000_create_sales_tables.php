<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('sales',function(Blueprint $t){
    $t->id();
    $t->string('invoice_no',50)->unique();
    $t->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
    $t->date('sale_date');
    $t->string('payment_status',20)->default('Pending');
    $t->decimal('subtotal',14,2)->default(0);
    $t->decimal('discount',14,2)->default(0);
    $t->decimal('invoice_discount',5,2)->default(0);
    $t->decimal('tax',14,2)->default(0);
    $t->decimal('grand_total',14,2)->default(0);
    $t->decimal('paid_amount',14,2)->default(0);
    $t->string('status',20)->default('Completed');
    $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $t->timestamps();
    $t->index(['customer_id','sale_date']);
    $t->index('payment_status');});
  Schema::create('sale_items',function(Blueprint $t){
    $t->id();
    $t->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
    $t->foreignId('medicine_id')->constrained('medicines')->restrictOnDelete();
    $t->foreignId('batch_id')->constrained('batches')->restrictOnDelete();
    $t->unsignedInteger('quantity')->default(1);
    $t->decimal('unit_price',14,2)->default(0);
    $t->decimal('discount',14,2)->default(0);
    $t->decimal('tax',14,2)->default(0);
    $t->decimal('total',14,2)->default(0);
    $t->timestamps();
    $t->index('batch_id');});
 }
 public function down(): void {Schema::dropIfExists('sale_items');Schema::dropIfExists('sales');}
};
