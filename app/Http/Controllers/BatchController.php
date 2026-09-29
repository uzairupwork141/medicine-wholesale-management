<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('medicine')->latest('id')->get();
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        return view('batches.create', ['medicines'=>Medicine::where('is_active',true)->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->ensureMedicineActive((int)$data['medicine_id']);
        if (Batch::where('medicine_id',$data['medicine_id'])->where('batch_no',$data['batch_no'])->exists()) {
            return back()->withErrors(['batch_no'=>'This batch number already exists for this medicine.'])->withInput();
        }
        Batch::create($data);
        return redirect()->route('batches.index')->with('success','Batch created successfully.');
    }

    public function edit(Batch $batch)
    {
        return view('batches.edit', ['batch'=>$batch,'medicines'=>Medicine::where('is_active',true)->orderBy('name')->get()]);
    }

    public function update(Request $request, Batch $batch)
    {
        $data = $this->validated($request);
        $this->ensureMedicineActive((int)$data['medicine_id']);
        if (Batch::where('medicine_id',$data['medicine_id'])->where('batch_no',$data['batch_no'])->where('id', '!=', $batch->id)->exists()) {
            return back()->withErrors(['batch_no'=>'This batch number already exists for this medicine.'])->withInput();
        }
        $batch->update($data);
        return redirect()->route('batches.index')->with('success','Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        if ($batch->quantity > 0) return back()->with('error','A batch with stock cannot be deleted.');
        $batch->delete();
        return redirect()->route('batches.index')->with('success','Batch deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'medicine_id'=>['required','integer','exists:medicines,id'],
            'batch_no'=>['required','string','max:50'],
            'manufacturing_date'=>['nullable','date'],
            'expiry_date'=>['required','date','after_or_equal:manufacturing_date'],
            'purchase_price'=>['required','numeric','min:0'],
            'sale_price'=>['required','numeric','min:0'],
            'mrp'=>['required','numeric','min:0'],
            'quantity'=>['required','integer','min:0'],
            'status'=>['required',Rule::in(['Available','Inactive'])],
        ]);
    }

    private function ensureMedicineActive(int $id): void
    {
        abort_unless(Medicine::whereKey($id)->where('is_active',true)->exists(), 422, 'Selected medicine is inactive.');
    }
}
