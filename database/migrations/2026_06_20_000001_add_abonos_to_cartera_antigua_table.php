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
        Schema::table('cartera_antigua', function (Blueprint $table) {
            $table->decimal('monto_pagado', 10, 2)->default(0)->after('deuda_total');
            $table->string('estado')->default('pendiente')->after('monto_pagado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cartera_antigua', function (Blueprint $table) {
            $table->dropColumn(['monto_pagado', 'estado']);
        });
    }
};
