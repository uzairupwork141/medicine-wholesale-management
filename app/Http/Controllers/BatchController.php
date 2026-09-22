<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Medicine;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('medicine')->get();

        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $medicines = Medicine::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('batches.create', compact('medicines'));
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'batch_no' => 'required|max:50',
            'manufacturing_date' => 'nullable|date',
            'expiry_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|max:20',
        ]);

        // Make sure the selected medicine is active
        $medicine = Medicine::where('id', $validated['medicine_id'])
            ->where('is_active', 1)
            ->first();

        if (!$medicine) {
            return back()
                ->withErrors([
                    'medicine_id' => 'The selected medicine is not available.'
                ])
                ->withInput();
        }

        // Prevent duplicate batch number for the same medicine
        $batchExists = Batch::where('medicine_id', $validated['medicine_id'])
            ->where('batch_no', $validated['batch_no'])
            ->exists();

        if ($batchExists) {
            return back()
                ->withErrors([
                    'batch_no' => 'This batch number already exists for this medicine.'
                ])
                ->withInput();
        }

        // Manufacturing date cannot be after expiry date
        if (
            !empty($validated['manufacturing_date']) &&
            $validated['manufacturing_date'] > $validated['expiry_date']
        ) {
            return back()
                ->withErrors([
                    'expiry_date' => 'Expiry date must be after manufacturing date.'
                ])
                ->withInput();
        }

        Batch::create($validated);

        return redirect('/batches');
    }

    public function edit(Batch $batch)
    {
        $medicines = Medicine::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('batches.edit', compact('batch', 'medicines'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'batch_no' => 'required|max:50',
            'manufacturing_date' => 'nullable|date',
            'expiry_date' => 'required|date|after:manufacturing_date',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'mrp' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'status' => 'required|max:20',
        ]);

        $batch->update($validated);

        return redirect('/batches');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect('/batches');
    }
}