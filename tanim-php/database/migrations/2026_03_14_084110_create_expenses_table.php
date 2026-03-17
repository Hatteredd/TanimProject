<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // electricity, water, internet, maintenance, salary, delivery, restock, other
            $table->string('label'); // human-readable label
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->boolean('recurring')->default(false);
            $table->string('recurring_period')->nullable(); // monthly, quarterly, yearly
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('expenses');
    }
};
