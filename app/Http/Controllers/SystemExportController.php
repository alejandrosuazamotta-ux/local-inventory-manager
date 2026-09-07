<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use ZipArchive;

use App\Exports\ProductsExport;
use App\Exports\CustomersExport;
use App\Exports\InvoicesExport;
use App\Exports\QuotesExport;
use App\Exports\CreditsExport;
use App\Exports\OverdueCreditsExport;
use App\Exports\PaymentsExport;
use App\Exports\SupplierDebtsExport;
use App\Exports\CarteraAntiguaExport;
use App\Exports\ExpensesExport;

class SystemExportController extends Controller
{
    public function exportZip(Request $request)
    {
        $tempFolder = null;

        try {
            // Directorio temporal único
            $tempFolder = 'export_temp_' . uniqid();
            Storage::disk('local')->makeDirectory($tempFolder);

            // Generar los archivos Excel para todos los módulos (excepto Contabilidad)
            Excel::store(new ProductsExport(),       "{$tempFolder}/01_Productos.xlsx",          'local');
            Excel::store(new CustomersExport(),      "{$tempFolder}/02_Clientes.xlsx",           'local');
            Excel::store(new InvoicesExport(),       "{$tempFolder}/03_Facturas_Ventas.xlsx",    'local');
            Excel::store(new QuotesExport(),         "{$tempFolder}/04_Cotizaciones.xlsx",       'local');
            Excel::store(new CreditsExport(),        "{$tempFolder}/05_Creditos_Cartera.xlsx",   'local');
            Excel::store(new OverdueCreditsExport(), "{$tempFolder}/06_Creditos_Vencidos.xlsx",  'local');
            Excel::store(new PaymentsExport(),       "{$tempFolder}/07_Abonos_Recibidos.xlsx",   'local');
            Excel::store(new SupplierDebtsExport(),  "{$tempFolder}/08_Deudas_Proveedores.xlsx", 'local');
            Excel::store(new CarteraAntiguaExport(), "{$tempFolder}/09_Cartera_Antigua.xlsx",    'local');
            Excel::store(new ExpensesExport(),       "{$tempFolder}/10_Gastos_Diarios.xlsx",      'local');

            // Ruta del ZIP
            $zipFileName = 'Copia_Seguridad_' . Carbon::now()->format('Y-m-d_H-i') . '.zip';
            $zipPath     = storage_path("app/{$zipFileName}");

            // Crear el ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("No se pudo crear el archivo ZIP en: {$zipPath}");
            }

            $files = Storage::disk('local')->files($tempFolder);
            foreach ($files as $file) {
                $absolutePath = Storage::disk('local')->path($file);
                if (file_exists($absolutePath)) {
                    $zip->addFile($absolutePath, basename($file));
                }
            }
            $zip->close();

            // Limpiar archivos Excel temporales
            Storage::disk('local')->deleteDirectory($tempFolder);

            // Descargar y eliminar el ZIP del servidor tras el envío
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            // Limpiar si hubo error
            if ($tempFolder) {
                Storage::disk('local')->deleteDirectory($tempFolder);
            }

            Log::error('Error en exportZip: ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return redirect()->back()->with('error', 'Error al generar el backup ZIP: ' . $e->getMessage());
        }
    }

    private function getDatesFromPeriod(string $period, Request $request)
    {
        $startDate = Carbon::today()->startOfDay();
        $endDate   = Carbon::today()->endOfDay();

        if ($period === 'week') {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
        } elseif ($period === 'month') {
            $startDate = Carbon::now()->startOfMonth();
        } elseif ($period === 'year') {
            $startDate = Carbon::now()->startOfYear();
        } elseif ($period === 'custom' && $request->has('start') && $request->has('end')) {
            $startDate = Carbon::parse($request->input('start'))->startOfDay();
            $endDate   = Carbon::parse($request->input('end'))->endOfDay();
        }

        return ['start' => $startDate, 'end' => $endDate];
    }
}
