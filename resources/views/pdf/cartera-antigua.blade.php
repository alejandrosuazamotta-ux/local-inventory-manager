@extends('pdf.layout')
@section('title', 'Reporte de Cartera Antigua')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;">#</th>
            <th>Cliente</th>
            <th class="text-right" style="width: 100px;">Deuda Total</th>
            <th class="text-right" style="width: 100px;">Monto Pagado</th>
            <th class="text-right" style="width: 110px;">Saldo Pendiente</th>
            <th class="text-center" style="width: 80px;">Estado</th>
            <th class="text-center" style="width: 100px;">Fecha Registro</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalDeuda = 0;
            $totalPagado = 0;
            $totalPendiente = 0;
        @endphp
        @forelse($data as $item)
            @php
                $pendiente = max(0, $item->deuda_total - $item->monto_pagado);
                $totalDeuda += $item->deuda_total;
                $totalPagado += $item->monto_pagado;
                $totalPendiente += $pendiente;
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="fw-bold">{{ $item->nombre_cliente }}</td>
                <td class="text-right">${{ number_format($item->deuda_total, 0, ',', '.') }}</td>
                <td class="text-right">${{ number_format($item->monto_pagado, 0, ',', '.') }}</td>
                <td class="text-right">${{ number_format($pendiente, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($item->estado == 'pagado')
                        <span style="color: #166534; font-weight: bold;">Pagado</span>
                    @else
                        <span style="color: #b91c1c; font-weight: bold;">Pendiente</span>
                    @endif
                </td>
                <td class="text-center">{{ $item->fecha_registro->format('d/m/Y') }}</td>
                <td>{{ $item->observaciones ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #6b7280;">No se encontraron registros de cartera antigua.</td>
            </tr>
        @endforelse
    </tbody>
    @if(count($data) > 0)
    <tfoot>
        <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #cbd5e1;">
            <td colspan="2" class="text-right fw-bold" style="padding: 8px;">TOTALES:</td>
            <td class="text-right" style="padding: 8px;">${{ number_format($totalDeuda, 0, ',', '.') }}</td>
            <td class="text-right" style="padding: 8px;">${{ number_format($totalPagado, 0, ',', '.') }}</td>
            <td class="text-right" style="padding: 8px; color: #b91c1c;">${{ number_format($totalPendiente, 0, ',', '.') }}</td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection
