<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('customers',function(Blueprint $t){
    $t->id();
    $t->string('customer_code',50)->unique();
    $t->string('business_name',100);
    $t->string('contact_person',100);
    $t->string('phone',20);
    $t->string('email',100)->nullable();
    $t->text('address')->nullable();
    $t->string('license_no',50)->nullable();
    $t->boolean('is_active')->default(true);
    $t->boolean('deleted')->default(false);
    $t->timestamps();
    });
  Schema::create('batches',function(Blueprint $t){
    $t->id();
    $t->foreignId('medicine_id')->constrained('medicines')->restrictOnDelete();
    $t->string('batch_no',50);
    $t->date('manufacturing_date')->nullable();
    $t->date('expiry_date');
    $t->decimal('purchase_price',14,2)->default(0);
    $t->decimal('sale_price',14,2)->default(0);
    $t->decimal('mrp',14,2)->default(0);
    $t->unsignedInteger('quantity')->default(0);
    $t->string('status',20)->default('Available');
    $t->timestamps();
    $t->unique(['medicine_id','batch_no']);
    $t->index(['medicine_id','expiry_date']);
    });
 }
 public function down(): void {Schema::dropIfExists('batches');Schema::dropIfExists('customers');}
};
