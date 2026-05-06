<?php

namespace App\Http\Controllers;

use App\Models\ExamBody;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamBodyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExamBody::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $examBodies = $query
            ->latest()
            ->paginate(20);

        return response()->json($examBodies);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exam_bodies,name'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $examBody = ExamBody::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'message' => 'Exam body created successfully.',
            'data' => $examBody,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamBody $examBody)
    {
        return response()->json($examBody);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, ExamBody $examBody)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:exam_bodies,name,' . $examBody->id,
            ],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $examBody->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'status' => $validated['status'] ?? $examBody->status,
        ]);

        return response()->json([
            'message' => 'Exam body updated successfully.',
            'data' => $examBody,
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(ExamBody $examBody)
    {
        $examBody->delete();

        return response()->json([
            'message' => 'Exam body deleted successfully.',
        ]);
    }
}