@extends('pdf.layout')
@section('title', 'Historial de Gastos')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 15%;">No. Gasto</th>
            <th style="width: 40%;">Descripción</th>
            <th style="width: 15%;">Fecha Gasto</th>
            <th style="width: 15%;">Fecha Registro</th>
            <th style="width: 15%;" class="text-right">Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>GST-{{ $item->id }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->date ? $item->date->format('d/m/Y') : '' }}</td>
            <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '' }}</td>
            <td class="text-right fw-bold text-danger">${{ number_format($item->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
