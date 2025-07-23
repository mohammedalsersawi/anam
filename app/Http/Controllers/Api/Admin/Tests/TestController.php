<?php

namespace App\Http\Controllers\Api\Admin\Tests;

use App\Models\Test;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    public function index()
    {
        try {
            $tests = Test::where('status', 1)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            if ($tests->isEmpty()) {
                return mainResponse(false, 'No active services found.', [], [], 404, null, false);
            }

            return mainResponse(true, 'Fetched category sections successfully.', compact('tests'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(StoreTestRequest  $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title', 'description'],
                ['price', 'rating', 'questions_count', 'age_from', 'age_to', 'status', 'category_id', 'rating_count']
            );
            $test = Test::create($data);
            if ($request->has('image')) {
                UploadImage($request->image, Test::PATH_IMAGE, Test::class, $test->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Test created successfully.', compact('test'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function update(UpdateTestRequest $request, $id)
    {
        try {
            $test = Test::findOrFail($id);

            $data = localizedRequestData(
                $request,
                ['title', 'description'],
                ['price', 'rating', 'questions_count', 'age_from', 'age_to', 'status', 'category_id', 'rating_count']
            );

            $test->update($data);

            if ($request->has('image')) {
                UploadImage($request->image, Test::PATH_IMAGE, Test::class, $test->id, true, null, Upload::IMAGE);
            }

            return mainResponse(true, 'Test updated successfully.', compact('test'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function destroy($id)
    {
        try {
            $test = Test::find($id);
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
        $result = toggleModelBooleanField(Test::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }
}
