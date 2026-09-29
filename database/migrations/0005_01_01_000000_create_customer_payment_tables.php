<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('customer_payments',function(Blueprint $t){$t->id();$t->foreignId('customer_id')->constrained('customers')->restrictOnDelete();$t->date('payment_date');$t->decimal('amount',14,2);$t->string('reference_no',100)->nullable();$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();$t->index(['customer_id','payment_date']);});
  Schema::create('payment_allocations',function(Blueprint $t){$t->id();$t->foreignId('customer_payment_id')->constrained('customer_payments')->cascadeOnDelete();$t->foreignId('sale_id')->constrained('sales')->restrictOnDelete();$t->decimal('amount',14,2);$t->unique(['customer_payment_id','sale_id']);$t->index('sale_id');});
 }
 public function down(): void {Schema::dropIfExists('payment_allocations');Schema::dropIfExists('customer_payments');}
};
