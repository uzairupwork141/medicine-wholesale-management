<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('deleted', 0)->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'required|max:50|unique:customers,customer_code',
            'business_name' => 'required|max:100',
            'contact_person' => 'required|max:100',
            'phone' => 'required|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable',
            'license_no' => 'nullable|max:50',
            'credit_limit' => 'required|numeric|min:0',
            'opening_balance' => 'required|numeric',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['deleted'] = 0;
        Customer::create($validated);

        return redirect('/customers');
    }

    public function edit(Customer $customer)
    {
        if ($customer->deleted) {
            abort(404);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        if ($customer->deleted) {
            abort(404);
        }

        $validated = $request->validate([
            'customer_code' => 'required|max:50|unique:customers,customer_code,' . $customer->id,
            'business_name' => 'required|max:100',
            'contact_person' => 'required|max:100',
            'phone' => 'required|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable',
            'license_no' => 'nullable|max:50',
            'credit_limit' => 'required|numeric|min:0',
            'opening_balance' => 'required|numeric',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        return redirect('/customers');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->deleted) {
            return redirect('/customers');
        }

        $customer->update([
            'deleted' => 1,
        ]);

        return redirect('/customers');
    }
    
}