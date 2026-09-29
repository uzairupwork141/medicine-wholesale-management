<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with(['category','manufacturer'])->orderBy('name')->get();
        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('medicines.create', ['categories'=>Category::orderBy('name')->get(), 'manufacturers'=>Manufacturer::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        Medicine::create($data);
        return redirect()->route('medicines.index')->with('success','Medicine created successfully.');
    }

    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('medicines.create', ['medicine'=>$medicine,'categories'=>Category::orderBy('name')->get(),'manufacturers'=>Manufacturer::orderBy('name')->get()]);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $data = $this->validated($request, $medicine->id);
        $data['is_active'] = $request->boolean('is_active');
        $medicine->update($data);
        return redirect()->route('medicines.index')->with('success','Medicine updated successfully.');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        if ($medicine->batches()->exists()) {
            return back()->with('error','This medicine has batches and cannot be deleted. Deactivate it instead.');
        }
        $medicine->delete();
        return redirect()->route('medicines.index')->with('success','Medicine deleted.');
    }

    private function validated(Request $request, ?int $ignoreId=null): array
    {
        return $request->validate([
            'product_code'=>['required','string','max:50','unique:medicines,product_code'.($ignoreId ? ','.$ignoreId : '')],
            'barcode'=>['nullable','string','max:100','unique:medicines,barcode'.($ignoreId ? ','.$ignoreId : '')],
            'name'=>['required','string','max:150'],
            'generic_name'=>['nullable','string','max:150'],
            'category_id'=>['required','integer','exists:categories,id'],
            'manufacturer_id'=>['required','integer','exists:manufacturers,id'],
            'dosage_form'=>['nullable','string','max:50'],
            'strength'=>['nullable','string','max:100'],
            'pack_size'=>['nullable','string','max:100'],
            'unit'=>['nullable','string','max:50'],
        ]);
    }
}
