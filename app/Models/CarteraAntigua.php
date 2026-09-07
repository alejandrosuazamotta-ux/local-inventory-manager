<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nombre_cliente
 * @property float $deuda_total
 * @property float $monto_pagado
 * @property string $estado
 * @property \Carbon\Carbon $fecha_registro
 * @property string|null $observaciones
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CarteraAntigua extends Model
{
    protected $table = 'cartera_antigua';

    protected $fillable = [
        'nombre_cliente',
        'deuda_total',
        'monto_pagado',
        'estado',
        'fecha_registro',
        'observaciones',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'deuda_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];
}
