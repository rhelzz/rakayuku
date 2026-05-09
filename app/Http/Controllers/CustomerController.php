<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Exports\CustomerExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::withCount('orders')
            ->search($request->search, ['name', 'email', 'phone', 'address'])
            ->dateRange($request->date_range, $request->start_date, $request->end_date)
            ->sort($request->sort_field, $request->sort_dir)
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'Pelanggan tidak bisa dihapus karena memiliki riwayat pesanan (data dibutuhkan untuk laporan).');
        }

        Customer::destroy($customer->id);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function export(Request $request)
    {    
        return Excel::download(new CustomerExport($request->start_date, $request->end_date), 'Daftar_Pelanggan_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
    