<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (! Schema::hasColumn('customers', 'credit_limit')) {
                    $table->decimal('credit_limit', 14, 2)->default(0)->after('license_no');
                }
                if (! Schema::hasColumn('customers', 'opening_balance')) {
                    $table->decimal('opening_balance', 14, 2)->default(0)->after('credit_limit');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'opening_balance')) $table->dropColumn('opening_balance');
                if (Schema::hasColumn('customers', 'credit_limit')) $table->dropColumn('credit_limit');
            });
        }
    }
};
