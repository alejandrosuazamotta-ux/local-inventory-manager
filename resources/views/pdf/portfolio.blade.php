@extends('pdf.layout')
@section('title', 'Reporte Consolidado de Cartera y Finanzas')
@section('content')
<div style="margin-bottom: 20px;">
    <strong>Periodo del Reporte:</strong> {{ $data['startDate']->format('d/m/Y') }} al {{ $data['endDate']->format('d/m/Y') }}
</div>

<h3 style="color:#1e3a8a; border-bottom:1px solid #1e3a8a; padding-bottom:5px;">1. Estado Global del Inventario</h3>
<table class="data-table" style="margin-bottom: 30px;">
    <tr>
        <th style="width: 50%;">Valor Total Invertido</th>
        <th style="width: 50%;">Utilidad Potencial (Ganancia Esperada)</th>
    </tr>
    <tr>
        <td class="text-center" style="font-size: 16px; font-weight:bold;">${{ number_format($data['inventario']->valor_total, 0, ',', '.') }}</td>
        <td class="text-center text-success" style="font-size: 16px; font-weight:bold;">${{ number_format($data['inventario']->utilidad_potencial, 0, ',', '.') }}</td>
    </tr>
</table>

<h3 style="color:#1e3a8a; border-bottom:1px solid #1e3a8a; padding-bottom:5px;">2. Rendimiento Comercial del Periodo</h3>
<table class="data-table" style="margin-bottom: 30px;">
    <tr>
        <th>Ventas Totales</th>
        <th>Utilidad Neta Real</th>
        <th>Margen Promedio</th>
    </tr>
    <tr>
        <td class="text-center fw-bold">${{ number_format($data['ventas_periodo'], 0, ',', '.') }}</td>
        <td class="text-center text-success fw-bold">${{ number_format($data['utilidad_periodo'], 0, ',', '.') }}</td>
        <td class="text-center fw-bold">
            {{ $data['ventas_periodo'] > 0 ? round(($data['utilidad_periodo'] / $data['ventas_periodo']) * 100, 1) : 0 }}%
        </td>
    </tr>
</table>

<h3 style="color:#1e3a8a; border-bottom:1px solid #1e3a8a; padding-bottom:5px;">3. Estado de Cartera (Créditos)</h3>
<table class="data-table">
    <tr>
        <th>Total Cuentas por Cobrar</th>
        <th>Total Abonos Recaudados (Periodo)</th>
        <th>Facturas Pendientes</th>
    </tr>
    <tr>
        <td class="text-center text-danger fw-bold">${{ number_format($data['cartera']['total_pendiente'], 0, ',', '.') }}</td>
        <td class="text-center fw-bold text-success">${{ number_format($data['abonos_periodo'], 0, ',', '.') }}</td>
        <td class="text-center fw-bold">{{ $data['cartera']['facturas_pendientes'] }}</td>
    </tr>
</table>

<br><br>
<h3 style="color:#1e3a8a; border-bottom:1px solid #1e3a8a; padding-bottom:5px;">Resumen de Estados de Crédito</h3>
<table style="width:100%; font-size:12px;">
    <tr>
        <td style="width:50%;">
            <ul>
                <li><strong class="text-danger">Vencidos:</strong> {{ $data['estado_creditos']['vencidas'] }}</li>
                <li><strong style="color:#f59e0b;">Pendientes (Sin Abonos):</strong> {{ $data['estado_creditos']['pendientes'] }}</li>
            </ul>
        </td>
        <td style="width:50%;">
            <ul>
                <li><strong style="color:#3b82f6;">Parcialmente Pagadas:</strong> {{ $data['estado_creditos']['parciales'] }}</li>
                <li><strong class="text-success">Pagadas Completamente:</strong> {{ $data['estado_creditos']['pagadas'] }}</li>
            </ul>
        </td>
    </tr>
</table>
@endsection