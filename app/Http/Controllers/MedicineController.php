<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Manufacturer;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with(['category', 'manufacturer'])->get();

        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        $categories = Category::all();
        $manufacturers = Manufacturer::all();

        return view('medicines.create', compact('categories', 'manufacturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required',
            'barcode' => 'nullable',
            'name' => 'required',
            'generic_name' => 'nullable',
            'category_id' => 'required',
            'manufacturer_id' => 'required',
            'dosage_form' => 'nullable',
            'strength' => 'nullable',
            'pack_size' => 'nullable',
            'unit' => 'nullable',
            'default_sale_price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'reorder_level' => 'required|integer',
            'is_active' => 'required',
        ]);

        Medicine::create([
            'product_code' => $request->product_code,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'generic_name' => $request->generic_name,
            'category_id' => $request->category_id,
            'manufacturer_id' => $request->manufacturer_id,
            'dosage_form' => $request->dosage_form,
            'strength' => $request->strength,
            'pack_size' => $request->pack_size,
            'unit' => $request->unit,
            'default_sale_price' => $request->default_sale_price,
            'mrp' => $request->mrp,
            'reorder_level' => $request->reorder_level,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('medicines.index');
    }

    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);

        $categories = Category::all();
        $manufacturers = Manufacturer::all();

        return view('medicines.create', compact(
            'medicine',
            'categories',
            'manufacturers'
        ));
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'product_code' => 'required',
            'barcode' => 'nullable',
            'name' => 'required',
            'generic_name' => 'nullable',
            'category_id' => 'required',
            'manufacturer_id' => 'required',
            'dosage_form' => 'nullable',
            'strength' => 'nullable',
            'pack_size' => 'nullable',
            'unit' => 'nullable',
            'default_sale_price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'reorder_level' => 'required|integer',
            'is_active' => 'required',
        ]);

        $medicine->product_code = $request->product_code;
        $medicine->barcode = $request->barcode;
        $medicine->name = $request->name;
        $medicine->generic_name = $request->generic_name;
        $medicine->category_id = $request->category_id;
        $medicine->manufacturer_id = $request->manufacturer_id;
        $medicine->dosage_form = $request->dosage_form;
        $medicine->strength = $request->strength;
        $medicine->pack_size = $request->pack_size;
        $medicine->unit = $request->unit;
        $medicine->default_sale_price = $request->default_sale_price;
        $medicine->mrp = $request->mrp;
        $medicine->reorder_level = $request->reorder_level;
        $medicine->is_active = $request->is_active;

        $medicine->save();

        return redirect()->route('medicines.index');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        $medicine->delete();

        return redirect()->route('medicines.index');
    }
}