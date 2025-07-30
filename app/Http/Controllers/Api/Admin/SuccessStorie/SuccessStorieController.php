<?php

namespace App\Http\Controllers\Api\Admin\SuccessStorie;

use App\Models\Upload;
use Illuminate\Http\Request;
use App\Models\SuccessStorie;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SuccessStorieRequest;
use App\Http\Requests\SuccessUpdateRequest;

class SuccessStorieController extends Controller
{
    public function getData()
    {
        $successStorie = SuccessStorie::with(['images'])->first();

        return mainResponse(true, 'Success Storie sections fetched.', compact('successStorie'), [], 200, null, false);
    }
    public function store(SuccessStorieRequest  $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title',],
                ['url_video']
            );
            $data['created_by'] = auth('admin')->id();
            $successStorie = SuccessStorie::create($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $item) {
                    UploadImage($item, SuccessStorie::PATH_IMAGE, SuccessStorie::class, $successStorie->id, false, null, Upload::IMAGE);
                }
            }
            $successStorie->load('images');
            return mainResponse(true, 'SuccessStorie created successfully.', compact('successStorie'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(true, 'SuccessStorie created successfully.', compact('successStorie'), [], 201, null, false);
        }
    }
    public function update(SuccessStorieRequest $request, $id)
    {
        try {
            $successStorie = SuccessStorie::findOrFail($id);
            $data = localizedRequestData(
                $request,
                ['title'],
                ['url_video']
            );
            $data['updated_by'] = auth('admin')->id();
            $successStorie->update($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $item) {
                    UploadImage($item, SuccessStorie::PATH_IMAGE, SuccessStorie::class, $successStorie->id, false, null, Upload::IMAGE, null, $index == 0);
                }
            }
            $successStorie->load('images');
            return mainResponse(true, 'SuccessStorie updated successfully.', compact('successStorie'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong during update.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
}
