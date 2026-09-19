<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

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

        Customer::create($validated);

        return redirect('/customers');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
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
        $customer->delete();

        return redirect('/customers');
    }
}