<?php

namespace App\Http\Controllers\Api\Admin\Course;

use App\Models\Course;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $course = Course::orderBy('created_at', 'desc')
                ->with('category:id,name')
                ->paginate(10);
            if ($course->isEmpty()) {
                return mainResponse(false, 'No active services found.', [], [], 404, null, false);
            }
            return mainResponse(true, 'Fetched Courses sections successfully.', compact('course'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(StoreCourseRequest  $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title', 'description'],
                ['price', 'rating', 'rating_count', 'hours', 'age_from', 'age_to', 'status', 'category_id'] // الحقول العادية
            );
            $data['created_by'] = auth('admin')->id();
            $course = Course::create($data);
            if ($request->has('image')) {
                UploadImage($request->image, Course::PATH_IMAGE, Course::class, $course->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Test created successfully.', compact('course'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function update(UpdateCourseRequest  $request, $id)
    {
        try {
            $course = Course::findOrFail($id);
            $data = localizedRequestData(
                $request,
                ['title', 'description'],
                ['price', 'rating', 'rating_count', 'hours', 'age_from', 'age_to', 'status', 'category_id'] // الحقول العادية
            );
            $data['updated_by'] = auth('admin')->id();
            $course->update($data);

            if ($request->has('image')) {
                UploadImage($request->image, Course::PATH_IMAGE, Course::class, $course->id, true, null, Upload::IMAGE);
            }

            return mainResponse(true, 'Test created successfully.', compact('course'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function destroy($id)
    {
        try {
            $test = Course::find($id);
            if (!$test) {
                return mainResponse(false, 'Test section not found.', [], [], 404, null, false);
            }
            $test->delete();
            return mainResponse(true, 'Test section deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(Course::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }
}
