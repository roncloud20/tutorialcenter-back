<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassAttendance;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_session_id' => 'required|exists:class_sessions,id',
            'student_id' => 'required|exists:students,id',
            'joined_at' => 'nullable|date',
            'attendance_duration' => 'nullable|integer',
            'left_at' => 'nullable|date',
            'status' => 'nullable|in:present,absent,late'
        ]);

        $attendance = ClassAttendance::create($validated);

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'data' => $attendance
        ], 201);
    }
}
