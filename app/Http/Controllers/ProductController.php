<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function export(Request $request)
    {
        $search = $request->input('search');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductsExport($search), 'inventario_productos.xlsx');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $products = Product::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['stock'] = intval($request->input('stock', 0));
        Product::create($data);
        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        if ($request->has('stock')) {
            $data['stock'] = intval($request->input('stock', 0));
        }
        $product->update($data);
        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $countSales = \Illuminate\Support\Facades\DB::table('sale_details')
            ->where('product_id', $product->id)
            ->distinct('sale_id')
            ->count('sale_id');

        if ($countSales > 0) {
            return redirect()->route('products.index')
                ->with('error', "No se puede eliminar \"{$product->name}\": tiene {$countSales} factura(s)/crédito(s) asociados. Marca el producto como inactivo en su lugar si ya no lo vendes.");
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function exportPdf(Request $request, PdfReportService $pdfService)
    {
        $search = $request->input('search');
        return $pdfService->generateProductsPdf($search);
    }

}
