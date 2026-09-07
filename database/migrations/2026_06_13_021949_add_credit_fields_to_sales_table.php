<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('invoice_type')->default('paid'); // paid, credit, quotation
            $table->date('due_date')->nullable();
            $table->decimal('initial_payment', 12, 2)->default(0);
            $table->decimal('pending_balance', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['invoice_type', 'due_date', 'initial_payment', 'pending_balance']);
        });
    }
};
