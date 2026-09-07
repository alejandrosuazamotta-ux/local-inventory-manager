@extends('pdf.layout')
@section('title', 'Reporte de Clientes')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th>#</th><th>Cliente</th><th>Cédula/NIT</th><th>Teléfono</th><th>Correo</th><th>Dirección</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->document_number ?? 'N/A' }}</td>
            <td>{{ $item->phone ?? 'N/A' }}</td>
            <td>{{ $item->email ?? 'N/A' }}</td>
            <td>{{ $item->address ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection