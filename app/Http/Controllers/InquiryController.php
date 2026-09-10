<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasPermission('view-inquiries')) {
            $inquiries = Inquiry::query()->latest();
        } else {
            $inquiries = Inquiry::query()->where('user_id', $user->id)->latest();
        }

        $inquiries = $inquiries->paginate(20);
        return view('inquiries.index', compact('inquiries'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create-inquiries'), 403);

        return view('inquiries.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('create-inquiries'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        Inquiry::create($validated + [
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('inquiries.index')->with('success', 'Inquiry submitted successfully.');
    }

    public function show(Inquiry $inquiry)
    {
        $user = auth()->user();

        if (!$user->hasPermission('view-inquiries') && $inquiry->user_id !== $user->id) {
            abort(403);
        }

        return view('inquiries.show', compact('inquiry'));
    }

    public function respond(Request $request, Inquiry $inquiry)
    {
        abort_unless(auth()->user()->hasPermission('respond-inquiries'), 403);

        $validated = $request->validate([
            'response' => 'required|string',
        ]);

        $inquiry->update([
            'response' => $validated['response'],
            'responded_by' => auth()->id(),
            'responded_at' => now(),
            'status' => 'resolved',
        ]);

        return redirect()->route('inquiries.index')->with('success', 'Inquiry responded successfully.');
    }
}


