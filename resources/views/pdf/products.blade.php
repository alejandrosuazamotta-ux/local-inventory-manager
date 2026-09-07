@extends('pdf.layout')
@section('title', 'Reporte de Productos')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 35%;">Nombre del Producto</th>
            <th style="width: 12%; text-align: center;">Cantidad</th>
            <th style="width: 12%; text-align: right;">P. Compra</th>
            <th style="width: 12%; text-align: right;">P. Venta</th>
            <th style="width: 12%; text-align: right;">Utilidad</th>
            <th style="width: 12%; text-align: center;">% Utilidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php
            $utilidad = $item->price - $item->cost;
            $porcentajeUtilidad = $item->price > 0 ? ($utilidad / $item->price) * 100 : 0;
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td style="font-weight: bold; color: #111827;">{{ $item->name }}</td>
            <td style="text-align: center;">{{ $item->stock }}</td>
            <td style="text-align: right;">${{ number_format($item->cost, 0, ',', '.') }}</td>
            <td style="text-align: right; color: #059669; font-weight: bold;">${{ number_format($item->price, 0, ',', '.') }}</td>
            <td style="text-align: right;">${{ number_format($utilidad, 0, ',', '.') }}</td>
            <td style="text-align: center;">{{ number_format($porcentajeUtilidad, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection