<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('employees')
                    ->nullOnDelete();
            }
        });

        DB::table('products')->whereNull('supplier_id')->orderBy('id')->chunk(200, function ($products): void {
            foreach ($products as $product) {
                $supplier = DB::table('employees')
                    ->where('location', $product->farm_location)
                    ->orderBy('id')
                    ->first();

                if ($supplier) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['supplier_id' => $supplier->id]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }
        });
    }
};
