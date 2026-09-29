<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('categories',function(Blueprint $t){$t->id();$t->string('name',100)->unique();$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('manufacturers',function(Blueprint $t){$t->id();$t->string('name',150)->unique();$t->string('phone',30)->nullable();$t->string('email',100)->nullable();$t->text('address')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('medicines',function(Blueprint $t){$t->id();$t->string('product_code',50)->unique();$t->string('barcode',100)->nullable()->unique();$t->string('name',150);$t->string('generic_name',150)->nullable();$t->foreignId('category_id')->constrained('categories')->restrictOnDelete();$t->foreignId('manufacturer_id')->constrained('manufacturers')->restrictOnDelete();$t->string('dosage_form',50)->nullable();$t->string('strength',100)->nullable();$t->string('pack_size',100)->nullable();$t->string('unit',50)->nullable();$t->boolean('is_active')->default(true);$t->timestamps();$t->index(['name','generic_name']);});
 }
 public function down(): void { Schema::dropIfExists('medicines');Schema::dropIfExists('manufacturers');Schema::dropIfExists('categories'); }
};
