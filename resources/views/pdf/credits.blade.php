@extends('pdf.layout')
@section('title', 'Historial de Créditos')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th>No. Crédito</th><th>Cliente</th><th>Emisión</th><th>Vencimiento</th><th>Estado</th><th class="text-right">Abonos</th><th class="text-right">Total</th><th class="text-right">Saldo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php
            $abonos = $item->payments->sum('amount');
            $saldo = $item->total - $abonos;
            $estado = $item->status == 'paid' ? 'Pagado' : 'Pendiente';
        @endphp
        <tr>
            <td>CRD-{{ $item->document_number }}</td>
            <td>{{ $item->customer->name ?? 'Consumidor Final' }}</td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
            <td>{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') : 'N/A' }}</td>
            <td>{{ $estado }}</td>
            <td class="text-right">${{ number_format($abonos, 0, ',', '.') }}</td>
            <td class="text-right">${{ number_format($item->total, 0, ',', '.') }}</td>
            <td class="text-right fw-bold">${{ number_format($saldo, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection