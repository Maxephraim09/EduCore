<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('view-visitors'), 403);

        $visitors = Visitor::query()->latest()->paginate(20);
        return view('visitors.index', compact('visitors'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create-visitors'), 403);

        return view('visitors.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('create-visitors'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'id_card' => 'nullable|string|max:50',
            'person_to_see' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'check_in' => 'required|date_format:H:i',
        ]);

        $visitor = Visitor::create($validated + [
            'checked_in_by' => auth()->id(),
            'status' => 'active',
        ]);

        return redirect()->route('visitors.index')->with('success', 'Visitor checked in successfully.');
    }

    public function show(Visitor $visitor)
    {
        abort_unless(auth()->user()->hasPermission('view-visitors'), 403);

        return view('visitors.show', compact('visitor'));
    }

    public function checkOut(Visitor $visitor)
    {
        abort_unless(auth()->user()->hasPermission('checkout-visitors'), 403);

        $visitor->update([
            'check_out' => now()->format('H:i'),
            'checked_out_by' => auth()->id(),
            'status' => 'completed',
        ]);

        return redirect()->route('visitors.index')->with('success', 'Visitor checked out successfully.');
    }
}


