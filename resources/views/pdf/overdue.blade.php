@extends('pdf.layout')
@section('title', 'Créditos Vencidos')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th>No. Crédito</th><th>Cliente</th><th>Vencimiento</th><th>Días Atraso</th><th class="text-right">Abonos</th><th class="text-right">Saldo Pendiente</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        @php
            $abonos = $item->payments->sum('amount');
            $saldo = $item->total - $abonos;
            $dias = 0;
            if($item->due_date) {
                $due = \Carbon\Carbon::parse($item->due_date);
                if($due->isPast()) {
                    $dias = $due->diffInDays(\Carbon\Carbon::now());
                }
            }
        @endphp
        <tr>
            <td>CRD-{{ $item->document_number }}</td>
            <td>{{ $item->customer->name ?? 'Consumidor Final' }}</td>
            <td>{{ \Carbon\Carbon::parse($item->due_date)->format('d/m/Y') }}</td>
            <td class="text-center text-danger fw-bold">{{ $dias }}</td>
            <td class="text-right">${{ number_format($abonos, 0, ',', '.') }}</td>
            <td class="text-right fw-bold">${{ number_format($saldo, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection