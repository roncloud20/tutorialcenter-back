<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * ADMIN: Create new course
     */
    public function store(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'required|string',
            'banner' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 2. Generate unique slug
            $slug = Str::slug($request->title);

            if (Course::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::random(6);
            }

            // 3. Upload banner
            $bannerPath = $request->file('banner')
                ->store('course_banners', 'public');

            // 4. Create course
            $course = Course::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'banner' => $bannerPath,
                'status' => $request->status,
                'price' => $request->price,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Course created successfully.',
                'course' => $course,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Course creation failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ADMIN: Update course
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found.',
            ], 404);
        }

        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255|unique:courses,title,' . $course->id,
            'description' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:active,inactive',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = $validator->validated();

            // 2. Handle title & slug update
            if (isset($data['title']) && $data['title'] !== $course->title) {
                $slug = Str::slug($data['title']);

                if (Course::where('slug', $slug)->where('id', '!=', $course->id)->exists()) {
                    $slug .= '-' . Str::random(6);
                }

                $data['slug'] = $slug;
            }

            // 3. Handle banner update
            if ($request->hasFile('banner')) {
                $data['banner'] = $request->file('banner')
                    ->store('course_banners', 'public');
            }

            // 4. Update course
            $course->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Course updated successfully.',
                'course' => $course->fresh(),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Course update failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ADMIN: Delete course (soft delete)
     */
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found.',
            ], 404);
        }

        try {
            $course->delete();

            return response()->json([
                'message' => 'Course deleted successfully.',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete course.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ADMIN: Restore soft-deleted course
     */
    public function restore(int $id): JsonResponse
    {
        // Only admins should reach here (middleware or policy)
        $course = Course::onlyTrashed()->find($id);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found or not deleted.',
            ], 404);
        }

        try {
            $course->restore();

            return response()->json([
                'message' => 'Course restored successfully.',
                'course' => $course,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Course restoration failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * STUDENT: Fetch active courses
     */
    public function index()
    {
        try {
            $courses = Course::where('status', 'active')->get();
            return response()->json([
                'courses' => $courses,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch courses.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

}
