<?php

namespace App\Http\Controllers\Api\Admin\Character;

use App\Models\Upload;
use App\Models\Setting;
use App\Models\Character;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateCharacterRequest;

class CharacterController extends Controller
{
    public function index()
    {
        try {
            $tests = Character::with('image')->orderBy('created_at', 'desc')
                ->paginate(10);
            if ($tests->isEmpty()) {
                return mainResponse(false, 'No Character services found.', [], [], 404, null, false);
            }
            return mainResponse(true, 'Fetched Character sections successfully.', compact('tests'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(StoreCharacterRequest  $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['name', 'description'],
                ['status', 'created_by', 'updated_by']
            );
            $data['created_by'] = auth('admin')->id();
            $test = Character::create($data);
            if ($request->has('image')) {
                UploadImage($request->image, Character::PATH_IMAGE, Character::class, $test->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Test created successfully.', compact('test'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function update(UpdateCharacterRequest $request, $id)
    {
        try {
            $character = Character::findOrFail($id);
            $data = localizedRequestData(
                $request,
                ['name', 'description'],
                ['status', 'updated_by']
            );
            $data['updated_by'] = auth('admin')->id();
            $character->update($data);
            if ($request->hasFile('image')) {
                UploadImage($request->image, Character::PATH_IMAGE, Character::class, $character->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Character updated successfully.', compact('character'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function destroy($id)
    {
        try {
            $test = Character::find($id);
            if (!$test) {
                return mainResponse(false, 'Character section not found.', [], [], 404, null, false);
            }
            $test->delete();
            return mainResponse(true, 'Character section deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(Character::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }

    public function getActivities($locale)
    {
        app()->setLocale($locale);

        $activities = Character::with('image')
            ->where('status', 1)
            ->latest()
            ->paginate(6);

        // تأكد أن فيه بيانات
        if ($activities->isEmpty()) {
            return mainResponse(false, 'No active activities found.', [], [], 404, null, false);
        }

        // معالجة العناصر داخل الـ collection
        $activities->getCollection()->transform(function ($Character) {
            return formatTranslatableData(
                $Character,
                ['title', 'description', 'facebook_url', 'instagram_url', 'button_text'],
                ['id'],
                ['image' => 'image']
            );
        });

        return mainResponse(true, 'Fetched Character sections successfully.', compact('activities'), [], 200);
    }

    public function storehero(Request $request)
    {
        try {
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['value_' . $key] = 'required|string|max:255';
                $rules['text_' . $key] = 'required|string|max:255';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422, null, false);
            }
            $data = localizedRequestData(
                $request,
                ['value', 'text'],
                ['updated_by']
            );
            $adminId = auth('admin')->id();
            $data['updated_by'] = $adminId;
            $setting = Setting::updateOrCreate(
                ['key' => 'Characters'],
                $data + ['created_by' => $adminId]
            );

            return mainResponse(true, 'Setting saved successfully.', compact('setting'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
}
