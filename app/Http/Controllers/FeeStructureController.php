<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with('class')->orderBy('fee_name')->paginate(15);
        return view('fee-payments.fee-structure', compact('feeStructures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fee_name' => 'required|string',
            'fee_code' => 'required|unique:fee_structures',
            'class_id' => 'nullable|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $payload = $request->only(['fee_name','fee_code','term','academic_year','amount','description']);
        $payload['fee_type'] = $request->input('fee_type', $request->fee_name);
        $payload['is_active'] = true;

        if ($request->filled('class_id')) {
            $cls = \App\Models\ClassModel::find($request->class_id);
            $payload['class_id'] = $cls->id;
            $payload['class'] = $cls->name;
        } else {
            $payload['class'] = $request->input('class', '');
        }

        FeeStructure::create($payload);

        return redirect()->route('fee-structure.index')
            ->with('success', 'Fee structure added successfully');
    }

    public function update(Request $request, $id)
    {
        $fee = FeeStructure::findOrFail($id);
        
        $request->validate([
            'fee_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $payload = $request->only(['fee_name','amount','description']);
        $payload['fee_type'] = $request->input('fee_type', $request->fee_name);
        if ($request->filled('class_id')) {
            $cls = \App\Models\ClassModel::find($request->class_id);
            $payload['class_id'] = $cls->id;
            $payload['class'] = $cls->name;
        }

        $fee->update($payload);

        return redirect()->route('fee-structure.index')
            ->with('success', 'Fee structure updated successfully');
    }

    public function destroy($id)
    {
        $fee = FeeStructure::findOrFail($id);
        $fee->delete();

        return redirect()->route('fee-structure.index')
            ->with('success', 'Fee structure deleted successfully');
    }
}
