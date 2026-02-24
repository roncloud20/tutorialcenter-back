<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSchedule;
use App\Models\ClassStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    /**
     * Display all classes
     */
    public function index(Request $request)
    {
        $classes = Classes::with([
            'staffs',
            'schedules',
            'sessions'
        ])
        ->when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->latest()
        ->paginate(10);

        return response()->json($classes);
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'staffs' => 'nullable|array',
            'staffs.*.staff_id' => 'required|exists:staff,id',
            'staffs.*.role' => 'nullable|string|max:255',
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        DB::beginTransaction();

        try {

            $class = Classes::create([
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            // Attach Staffs
            if (!empty($validated['staffs'])) {
                $staffData = [];
                foreach ($validated['staffs'] as $staff) {
                    $staffData[$staff['staff_id']] = [
                        'role' => $staff['role'] ?? null
                    ];
                }
                $class->staffs()->attach($staffData);
            }

            // Create schedules
            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $schedule) {
                    ClassSchedule::create([
                        'class_id' => $class->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Class created successfully',
                'data' => $class->load('staffs', 'schedules')
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display specific class
     */
    public function show($id)
    {
        $class = Classes::with([
            'staffs',
            'schedules',
            'sessions'
        ])->findOrFail($id);

        return response()->json($class);
    }

    /**
     * Update class
     */
    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'sometimes|exists:subjects,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
            'staffs' => 'nullable|array',
            'staffs.*.staff_id' => 'required|exists:staff,id',
            'staffs.*.role' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {

            $class->update($validated);

            // Sync staffs if provided
            if ($request->has('staffs')) {
                $staffData = [];
                foreach ($validated['staffs'] as $staff) {
                    $staffData[$staff['staff_id']] = [
                        'role' => $staff['role'] ?? null
                    ];
                }
                $class->staffs()->sync($staffData);
            }

            DB::commit();

            return response()->json([
                'message' => 'Class updated successfully',
                'data' => $class->load('staffs', 'schedules')
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete class
     */
    public function destroy($id)
    {
        $class = Classes::findOrFail($id);
        $class->delete();

        return response()->json([
            'message' => 'Class deleted successfully'
        ]);
    }

    /**
     * Restore soft deleted class
     */
    public function restore($id)
    {
        $class = Classes::withTrashed()->findOrFail($id);
        $class->restore();

        return response()->json([
            'message' => 'Class restored successfully'
        ]);
    }

    /**
     * Permanently delete class
     */
    public function forceDelete($id)
    {
        $class = Classes::withTrashed()->findOrFail($id);
        $class->forceDelete();

        return response()->json([
            'message' => 'Class permanently deleted'
        ]);
    }

    /**
     * Update class status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $class = Classes::findOrFail($id);
        $class->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $class
        ]);
    }

    /**
     * Attach staff to class
     */
    public function attachStaff(Request $request, $id)
    {
        $class = Classes::findOrFail($id);

        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'role' => 'nullable|string|max:255'
        ]);

        $class->staffs()->attach($validated['staff_id'], [
            'role' => $validated['role'] ?? null
        ]);

        return response()->json([
            'message' => 'Staff attached successfully'
        ]);
    }

    /**
     * Detach staff from class
     */
    public function detachStaff($classId, $staffId)
    {
        $class = Classes::findOrFail($classId);
        $class->staffs()->detach($staffId);

        return response()->json([
            'message' => 'Staff detached successfully'
        ]);
    }
}
