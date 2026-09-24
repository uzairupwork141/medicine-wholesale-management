<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  if(!Schema::hasTable('sales')) Schema::create('sales',function(Blueprint $t){$t->id();$t->string('invoice_no',50)->unique();$t->foreignId('customer_id')->constrained('customers')->restrictOnDelete();$t->foreignId('user_id')->constrained('users')->restrictOnDelete();$t->date('sale_date');$t->decimal('subtotal',14,2)->default(0);$t->decimal('discount',14,2)->default(0);$t->decimal('invoice_discount',14,2)->default(0);$t->decimal('grand_total',14,2)->default(0);$t->decimal('paid_amount',14,2)->default(0);$t->decimal('due_amount',14,2)->default(0);$t->string('payment_method',20)->default('cash');$t->string('payment_status',20)->default('paid');$t->text('notes')->nullable();$t->timestamps();$t->index(['customer_id','sale_date']);});
  else $this->missing('sales',[
   'invoice_no'=>fn($t)=>$t->string('invoice_no',50)->nullable(),'customer_id'=>fn($t)=>$t->unsignedBigInteger('customer_id')->nullable(),'user_id'=>fn($t)=>$t->unsignedBigInteger('user_id')->nullable(),'sale_date'=>fn($t)=>$t->date('sale_date')->nullable(),'subtotal'=>fn($t)=>$t->decimal('subtotal',14,2)->default(0),'discount'=>fn($t)=>$t->decimal('discount',14,2)->default(0),'invoice_discount'=>fn($t)=>$t->decimal('invoice_discount',14,2)->default(0),'grand_total'=>fn($t)=>$t->decimal('grand_total',14,2)->default(0),'paid_amount'=>fn($t)=>$t->decimal('paid_amount',14,2)->default(0),'due_amount'=>fn($t)=>$t->decimal('due_amount',14,2)->default(0),'payment_method'=>fn($t)=>$t->string('payment_method',20)->default('cash'),'payment_status'=>fn($t)=>$t->string('payment_status',20)->default('paid'),'notes'=>fn($t)=>$t->text('notes')->nullable()]);
  if(!Schema::hasTable('sale_items')) Schema::create('sale_items',function(Blueprint $t){$t->id();$t->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();$t->foreignId('medicine_id')->constrained('medicines')->restrictOnDelete();$t->foreignId('batch_id')->constrained('batches')->restrictOnDelete();$t->unsignedInteger('quantity');$t->decimal('unit_price',14,2);$t->decimal('mrp',14,2);$t->decimal('discount',14,2)->default(0);$t->decimal('total',14,2);$t->timestamps();$t->index(['medicine_id','batch_id']);});
  else $this->missing('sale_items',[
   'sale_id'=>fn($t)=>$t->unsignedBigInteger('sale_id')->nullable(),'medicine_id'=>fn($t)=>$t->unsignedBigInteger('medicine_id')->nullable(),'batch_id'=>fn($t)=>$t->unsignedBigInteger('batch_id')->nullable(),'quantity'=>fn($t)=>$t->unsignedInteger('quantity')->default(0),'unit_price'=>fn($t)=>$t->decimal('unit_price',14,2)->default(0),'mrp'=>fn($t)=>$t->decimal('mrp',14,2)->default(0),'discount'=>fn($t)=>$t->decimal('discount',14,2)->default(0),'total'=>fn($t)=>$t->decimal('total',14,2)->default(0)]);
 }
 private function missing(string $table,array $columns):void{foreach($columns as $name=>$def)if(!Schema::hasColumn($table,$name))Schema::table($table,$def);}
 public function down():void{Schema::dropIfExists('sale_items');Schema::dropIfExists('sales');}
};
