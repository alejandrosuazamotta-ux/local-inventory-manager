<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SystemExportController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\SaleController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ExpenseController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Auth::routes([
    'register' => false, // Solo el administrador existe, nadie más puede registrarse
    'reset' => false,    // Deshabilitar recuperación si no hay servidor de correo
    'verify' => false,
]);


Route::middleware(['auth'])->group(function () {
    Route::post('/admin/logo', [DashboardController::class, 'uploadLogo'])->name('admin.logo.upload');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/exportar', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('/export-all', [SystemExportController::class, 'exportZip'])->name('export.all');
    Route::get('/cartera', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::get('/cartera/exportar/excel', [PortfolioController::class, 'exportExcel'])->name('portfolio.export.excel');
    Route::get('/cartera/exportar/pdf', [PortfolioController::class, 'exportPdf'])->name('portfolio.export.pdf');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::get('/customers/export/pdf', [CustomerController::class, 'exportPdf'])->name('customers.export.pdf');
    Route::get('/customers/autocomplete', [CustomerController::class, 'autocomplete'])->name('clients.autocomplete');
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
    
    // Facturación
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales/store', [SaleController::class, 'store'])->name('invoices.store');
    Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::put('/sales/{sale}/general', [SaleController::class, 'updateGeneral'])->name('sales.updateGeneral');
    Route::get('/facturas', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/facturas/exportar', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::get('/facturas/exportar/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.export.pdf');
    Route::get('/facturas/{sale}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download.pdf');
    Route::get('/cotizaciones', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/cotizaciones/exportar', [QuoteController::class, 'export'])->name('quotes.export');
    Route::get('/cotizaciones/exportar/pdf', [QuoteController::class, 'exportPdf'])->name('quotes.export.pdf');
    Route::get('/cotizaciones/{sale}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.download.pdf');
    Route::get('/creditos/vencidos', [CreditController::class, 'overdue'])->name('credits.overdue');
    Route::get('/creditos/vencidos/exportar', [CreditController::class, 'exportOverdue'])->name('credits.exportOverdue');
    Route::get('/creditos/vencidos/exportar/pdf', [CreditController::class, 'exportOverduePdf'])->name('credits.overdue.export.pdf');
    Route::get('/creditos', [CreditController::class, 'index'])->name('credits.index');
    Route::get('/creditos/pagados', [CreditController::class, 'paid'])->name('credits.paid');
    Route::get('/creditos/exportar', [CreditController::class, 'export'])->name('credits.export');
    Route::get('/creditos/exportar/pdf', [CreditController::class, 'exportPdf'])->name('credits.export.pdf');
    Route::get('/creditos/{sale}/pdf', [CreditController::class, 'downloadPdf'])->name('credits.download.pdf');
    Route::get('/abonos', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/abonos/exportar', [PaymentController::class, 'export'])->name('payments.export');
    Route::get('/abonos/exportar/pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
    Route::post('/abonos/store', [PaymentController::class, 'store'])->name('payments.store');
    Route::put('/abonos/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/abonos/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Deudas a Proveedores
    Route::get('/supplier-debts/exportar', [\App\Http\Controllers\SupplierDebtController::class, 'export'])->name('supplier-debts.export');
    Route::get('/supplier-debts/exportar/pdf', [\App\Http\Controllers\SupplierDebtController::class, 'exportPdf'])->name('supplier-debts.export.pdf');
    Route::get('/supplier-debts', [\App\Http\Controllers\SupplierDebtController::class, 'index'])->name('supplier-debts.index');
    Route::post('/supplier-debts', [\App\Http\Controllers\SupplierDebtController::class, 'store'])->name('supplier-debts.store');
    Route::put('/supplier-debts/{supplierDebt}', [\App\Http\Controllers\SupplierDebtController::class, 'update'])->name('supplier-debts.update');
    Route::delete('/supplier-debts/{supplierDebt}', [\App\Http\Controllers\SupplierDebtController::class, 'destroy'])->name('supplier-debts.destroy');
    Route::post('/supplier-debts/{supplierDebt}/pay', [\App\Http\Controllers\SupplierDebtController::class, 'pay'])->name('supplier-debts.pay');

    // Cartera Antigua
    Route::get('/cartera-antigua/exportar/excel', [\App\Http\Controllers\CarteraAntiguaController::class, 'exportExcel'])->name('cartera-antigua.export.excel');
    Route::get('/cartera-antigua/exportar/pdf', [\App\Http\Controllers\CarteraAntiguaController::class, 'exportPdf'])->name('cartera-antigua.export.pdf');
    Route::post('/cartera-antigua/{carteraAntigua}/pay', [\App\Http\Controllers\CarteraAntiguaController::class, 'pay'])->name('cartera-antigua.pay');
    Route::resource('cartera-antigua', \App\Http\Controllers\CarteraAntiguaController::class)->only(['index', 'store', 'update', 'destroy']);

    // Gastos
    Route::get('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
    Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'update', 'destroy']);
});
