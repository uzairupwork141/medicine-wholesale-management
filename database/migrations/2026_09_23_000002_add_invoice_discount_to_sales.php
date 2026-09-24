<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { if(Schema::hasTable('sales')&&!Schema::hasColumn('sales','invoice_discount'))Schema::table('sales',fn(Blueprint $t)=>$t->decimal('invoice_discount',14,2)->default(0)->after('discount')); }
 public function down(): void { if(Schema::hasTable('sales')&&Schema::hasColumn('sales','invoice_discount'))Schema::table('sales',fn(Blueprint $t)=>$t->dropColumn('invoice_discount')); }
};
