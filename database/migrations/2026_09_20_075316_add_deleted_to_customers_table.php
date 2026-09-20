<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'deleted')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->boolean('deleted')
                    ->default(false)
                    ->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'deleted')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('deleted');
            });
        }
    }
};