<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manufacturer;

class ManufacturerController extends Controller
{
    public function index()
    {
        $manufacturers = Manufacturer::all();

        return view('manufacturers.index', compact('manufacturers'));
    }

    public function create()
    {
        return view('manufacturers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'is_active' => 'required',
        ]);

        Manufacturer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('manufacturers.index');
    }

    public function edit($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return view('manufacturers.create', compact('manufacturer'));
    }

    public function update(Request $request, $id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'is_active' => 'required',
        ]);

        $manufacturer->name = $request->name;
        $manufacturer->phone = $request->phone;
        $manufacturer->email = $request->email;
        $manufacturer->address = $request->address;
        $manufacturer->is_active = $request->is_active;

        $manufacturer->save();

        return redirect()->route('manufacturers.index');
    }

    public function destroy($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        $manufacturer->delete();

        return redirect()->route('manufacturers.index');
    }
}