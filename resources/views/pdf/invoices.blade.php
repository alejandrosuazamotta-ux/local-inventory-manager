@extends('pdf.layout')
@section('title', 'Historial de Facturas')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th>No. Factura</th><th>Cliente</th><th>Documento</th><th>Teléfono</th><th>Fecha</th><th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>FAC-{{ $item->document_number }}</td>
            <td>{{ $item->customer->name ?? 'Consumidor Final' }}</td>
            <td>{{ $item->customer->document_number ?? 'N/A' }}</td>
            <td>{{ $item->customer->phone ?? 'N/A' }}</td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
            <td class="text-right">${{ number_format($item->total, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection