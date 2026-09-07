<?php

namespace App\Http\Controllers;

use App\Reports\PDF\PdfReportService;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $customers = Customer::with(['sales' => function ($query) {
                $query->latest();
            }])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('document_number', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());
        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Customer $customer)
    {
        return view('admin.clientes.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.clientes.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return redirect()->route('customers.index')
                ->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')
                ->with('error', 'No se pudo eliminar el cliente. Es posible que tenga registros asociados.');
        }
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomersExport, 'clientes_directorio.xlsx');
    }

    public function autocomplete(Request $request)
    {
        $q = $request->input('q');
        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('document_number', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('address', 'like', "%{$q}%")
            ->take(10)
            ->get();

        $results = $customers->map(function ($c) {
            return [
                'name' => $c->name,
                'cedula' => $c->document_number,
                'cellphone' => $c->phone,
                'email' => $c->email,
                'address' => $c->address
            ];
        });

        return response()->json($results);
    }

    public function exportPdf(PdfReportService $pdfService)
    {
        return $pdfService->generateCustomersPdf();
    }

}
