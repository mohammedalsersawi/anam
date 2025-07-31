<?php

namespace App\Http\Controllers\Api\Admin\Activity;

use App\Models\Upload;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;

class ActivityController extends Controller
{
    public function index()
    {
        try {
            $tests = Activity::orderBy('created_at', 'desc')
                ->paginate(10);
            if ($tests->isEmpty()) {
                return mainResponse(false, 'No Activity services found.', [], [], 404, null, false);
            }
            return mainResponse(true, 'Fetched Activity sections successfully.', compact('tests'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(StoreActivityRequest  $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title', 'description', 'facebook_url', 'instagram_url', 'button_text'],
                ['status', 'created_by', 'updated_by']
            );
            $data['created_by'] = auth('admin')->id();
            $test = Activity::create($data);
            if ($request->has('image')) {
                UploadImage($request->image, Activity::PATH_IMAGE, Activity::class, $test->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Test created successfully.', compact('test'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function update(UpdateActivityRequest $request, $id)
    {
        try {
            $activity = Activity::findOrFail($id);

            $data = localizedRequestData(
                $request,
                ['title', 'description', 'facebook_url', 'instagram_url', 'button_text'],
                ['status', 'updated_by']
            );
            $data['updated_by'] = auth('admin')->id();
            $activity->update($data);
            if ($request->has('image')) {
                UploadImage($request->image, Activity::PATH_IMAGE, Activity::class, $activity->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Activity updated successfully.', compact('activity'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function destroy($id)
    {
        try {
            $test = Activity::find($id);
            if (!$test) {
                return mainResponse(false, 'Activity section not found.', [], [], 404, null, false);
            }
            $test->delete();
            return mainResponse(true, 'Activity section deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(Activity::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }

    public function getActivities($locale)
    {
        app()->setLocale($locale);

        $activities = Activity::with('image')
            ->where('status', 1)
            ->latest()
            ->paginate(6);

        // تأكد أن فيه بيانات
        if ($activities->isEmpty()) {
            return mainResponse(false, 'No active activities found.', [], [], 404, null, false);
        }

        // معالجة العناصر داخل الـ collection
        $activities->getCollection()->transform(function ($activity) {
            return formatTranslatableData(
                $activity,
                ['title', 'description', 'facebook_url', 'instagram_url', 'button_text'],
                ['id'],
                ['image' => 'image']
            );
        });

            return mainResponse(true, 'Fetched Activity sections successfully.', compact('activities'), [], 200);
    }
}
