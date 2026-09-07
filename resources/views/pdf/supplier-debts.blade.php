@extends('pdf.layout')
@section('title', 'Reporte de Deudas a Proveedores')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Proveedor</th>
            <th>Total Deuda</th>
            <th>Total Pagado</th>
            <th>Saldo Pendiente</th>
            <th>Estado</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>${{ number_format($item->debt_amount, 0, ',', '.') }}</td>
            <td>${{ number_format($item->paid_amount, 0, ',', '.') }}</td>
            <td>${{ number_format($item->debt_amount - $item->paid_amount, 0, ',', '.') }}</td>
            <td>
                @if($item->status == 'pagado')
                    <span style="color: #166534;">Pagado</span>
                @else
                    <span style="color: #991b1b;">Pendiente</span>
                @endif
            </td>
            <td>{{ $item->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
