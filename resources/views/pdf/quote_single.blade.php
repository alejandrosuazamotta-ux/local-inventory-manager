@extends('pdf.layout')

@section('title', 'Cotización #' . $quote->document_number)

@section('content')
<div style="margin-bottom: 20px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Cliente:</strong> {{ $quote->customer->name ?? 'Consumidor Final' }}<br>
                @if($quote->customer && $quote->customer->document_number)
                <strong>Documento:</strong> {{ $quote->customer->document_number }}<br>
                @endif
                @if($quote->customer && $quote->customer->phone)
                <strong>Teléfono:</strong> {{ $quote->customer->phone }}<br>
                @endif
                @if($quote->customer && $quote->customer->address)
                <strong>Dirección:</strong> {{ $quote->customer->address }}
                @endif
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <strong>Cotización No:</strong> #COT-{{ $quote->document_number }}<br>
                <strong>Fecha Emisión:</strong> {{ $quote->created_at->format('d/m/Y') }}<br>
                <strong>Tipo:</strong> Cotización
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
            @foreach($quote->details as $detail)
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
            <td style="padding: 10px 5px; text-align: right; font-size: 14px; background: #f3f4f6;"><strong>TOTAL ESTIMADO:</strong></td>
            <td style="padding: 10px 5px; text-align: right; font-size: 14px; background: #f3f4f6; color: #16a34a;"><strong>${{ number_format($quote->total, 0, ',', '.') }}</strong></td>
        </tr>
    </table>
    <div style="clear: both;"></div>
</div>

<div style="margin-top: 60px; text-align: center; color: #6b7280; border-top: 1px dashed #d1d5db; padding-top: 10px;">
    Esta cotización tiene una validez de 30 días a partir de su emisión.<br>
    Gracias por su preferencia.
</div>
@endsection
