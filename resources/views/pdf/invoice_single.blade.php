@extends('pdf.layout')

@section('title', ($invoice->invoice_type == 'credit' ? 'Crédito #' : 'Factura #') . $invoice->document_number)

@section('content')
<div style="margin-bottom: 20px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Cliente:</strong> {{ $invoice->customer->name ?? 'Consumidor Final' }}<br>
                @if($invoice->customer && $invoice->customer->document_number)
                <strong>Documento:</strong> {{ $invoice->customer->document_number }}<br>
                @endif
                @if($invoice->customer && $invoice->customer->phone)
                <strong>Teléfono:</strong> {{ $invoice->customer->phone }}<br>
                @endif
                @if($invoice->customer && $invoice->customer->address)
                <strong>Dirección:</strong> {{ $invoice->customer->address }}
                @endif
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>{{ $invoice->invoice_type == 'credit' ? 'Crédito No:' : 'Factura No:' }}</strong> #{{ $invoice->invoice_type == 'credit' ? 'CRD' : 'FAC' }}-{{ $invoice->document_number }}<br>
                <strong>Fecha Emisión:</strong> {{ $invoice->created_at->format('d/m/Y') }}<br>
                <strong>Tipo:</strong> {{ $invoice->invoice_type == 'paid' ? 'Contado' : 'Crédito' }}<br>
                <strong>Estado:</strong> {{ $invoice->status == 'paid' ? 'Pagada' : 'Pendiente' }}
            </td>
        </tr>
    </table>
</div>

<div style="margin-bottom: 20px;">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%; text-align: center;">CANT</th>
                <th style="width: 45%;">PRODUCTO</th>
                <th style="width: 15%; text-align: right;">P. UNITARIO</th>
                <th style="width: 15%; text-align: right;">DESCUENTO</th>
                <th style="width: 15%; text-align: right;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDescuento = 0;
                $subtotalBruto = 0;
            @endphp
            @foreach($invoice->details as $detail)
                @php
                    $precioBase = $detail->unit_price * $detail->quantity;
                    $descuentoLinea = $precioBase > 0 ? ($precioBase - $detail->subtotal) : 0;
                    $totalDescuento += $descuentoLinea;
                    $subtotalBruto += $precioBase;
                @endphp
                <tr>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td>{{ $detail->product->name ?? 'Producto Eliminado' }}</td>
                    <td class="text-right">${{ number_format($detail->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right text-danger">{{ $descuentoLinea > 0 ? '-$'.number_format($descuentoLinea, 0, ',', '.') : '$0' }}</td>
                    <td class="text-right fw-bold">${{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="width: 100%; text-align: right; margin-top: 20px;">
    <table style="width: 40%; float: right; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px; text-align: right; border-bottom: 1px solid #e5e7eb;"><strong>Subtotal:</strong></td>
            <td style="padding: 5px; text-align: right; border-bottom: 1px solid #e5e7eb;">${{ number_format($subtotalBruto, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="padding: 5px; text-align: right; border-bottom: 1px solid #e5e7eb;"><strong>Descuento:</strong></td>
            <td style="padding: 5px; text-align: right; border-bottom: 1px solid #e5e7eb; color: #dc2626;">-${{ number_format($totalDescuento, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="padding: 10px 5px; text-align: right; font-size: 14px; background: #f3f4f6;"><strong>TOTAL A PAGAR:</strong></td>
            <td style="padding: 10px 5px; text-align: right; font-size: 14px; background: #f3f4f6; color: #16a34a;"><strong>${{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
        </tr>
        @if($invoice->invoice_type == 'credit')
        <tr>
            <td style="padding: 5px; text-align: right;"><strong>Abonos Totales:</strong></td>
            <td style="padding: 5px; text-align: right;">${{ number_format($invoice->total - $invoice->pending_balance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="padding: 5px; text-align: right; border-top: 1px solid #e5e7eb;"><strong>Saldo Pendiente:</strong></td>
            <td style="padding: 5px; text-align: right; border-top: 1px solid #e5e7eb; color: #dc2626;"><strong>${{ number_format($invoice->pending_balance, 0, ',', '.') }}</strong></td>
        </tr>
        @endif
    </table>
    <div style="clear: both;"></div>
</div>

<div style="margin-top: 60px; text-align: center; color: #6b7280; border-top: 1px dashed #d1d5db; padding-top: 10px;">
    Gracias por su preferencia.
</div>
@endsection
