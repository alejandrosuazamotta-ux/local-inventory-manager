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
            $table->integer('document_number')->nullable()->after('invoice_type');
        });

        // Backfill existing records
        $types = ['paid', 'credit', 'quotation'];
        foreach ($types as $type) {
            $sales = \App\Models\Sale::where('invoice_type', $type)->orderBy('id')->get();
            $counter = 1;
            foreach ($sales as $sale) {
                $sale->document_number = $counter++;
                $sale->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('document_number');
        });
    }
};
