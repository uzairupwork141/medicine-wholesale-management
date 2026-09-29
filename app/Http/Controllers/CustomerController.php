<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('deleted', false)->orderBy('business_name')->get();
        return view('customers.index', compact('customers'));
    }

    public function create() { return view('customers.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => ['required','string','max:50','unique:customers,customer_code'],
            'business_name' => ['required','string','max:100'],
            'contact_person' => ['required','string','max:100'],
            'phone' => ['required','string','max:20'],
            'email' => ['nullable','email','max:100'],
            'address' => ['nullable','string','max:2000'],
            'license_no' => ['nullable','string','max:50'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['deleted'] = false;
        Customer::create($validated);
        return redirect()->route('customers.index')->with('success','Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        abort_if($customer->deleted, 404);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if($customer->deleted, 404);
        $validated = $request->validate([
            'customer_code' => ['required','string','max:50','unique:customers,customer_code,'.$customer->id],
            'business_name' => ['required','string','max:100'],
            'contact_person' => ['required','string','max:100'],
            'phone' => ['required','string','max:20'],
            'email' => ['nullable','email','max:100'],
            'address' => ['nullable','string','max:2000'],
            'license_no' => ['nullable','string','max:50'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $customer->update($validated);
        return redirect()->route('customers.index')->with('success','Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        abort_if($customer->deleted, 404);
        $customer->update(['deleted'=>true]);
        return redirect()->route('customers.index')->with('success','Customer deactivated.');
    }
}
