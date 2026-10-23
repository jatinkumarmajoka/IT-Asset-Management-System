<?php

namespace App\Http\Controllers;

use App\Models\OperationalRequest;
use Illuminate\Http\Request;

class OperationalRequestController extends Controller
{
    public function index()
    {
        $requests = OperationalRequest::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('operational-requests.index', compact('requests'));
    }

    public function create()
    {
        return view('operational-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:100',
                'in:Software Installation,Hardware Repair,Hardware Request,Accessories,Other',
            ],
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $user = $request->user();

        OperationalRequest::create([
            'user_id' => $user->id,
            'department_id' => $user->department_id,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()
            ->route('operational-requests.index')
            ->with('success', 'Your operational request has been submitted.');
    }
}
