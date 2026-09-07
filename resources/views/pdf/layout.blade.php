<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            padding: 0;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .report-title {
            font-size: 18px;
            color: #4b5563;
            text-align: right;
        }
        .date {
            font-size: 10px;
            color: #6b7280;
            text-align: right;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.data-table th {
            background-color: #2563eb;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #1e3a8a;
        }
        table.data-table td {
            padding: 7px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="company-name">{{ config('app.name', 'Mi Negocio') }}</div>
                    <div style="font-size:11px; margin-top:5px; color:#6b7280;">Reporte Generado por el Sistema</div>
                </td>
                <td style="text-align:right;">
                    <div class="report-title">@yield('title')</div>
                    <div class="date">Fecha: {{ date('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    @yield('content')

    <div class="footer">
        Página <span class="pagenum"></span> - Documento Confidencial
    </div>
</body>
</html>
