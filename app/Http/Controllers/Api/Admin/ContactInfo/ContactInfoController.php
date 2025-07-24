<?php

namespace App\Http\Controllers\Api\Admin\ContactInfo;

use App\Models\Upload;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreContactInfoRequest;
use App\Http\Requests\UpdateContactInfoRequest;

class ContactInfoController extends Controller
{

    public function getData()
    {
        $contactInfo = ContactInfo::with(['images'])->first();

        return mainResponse(true, 'ContactInfo sections fetched.', compact('contactInfo'), [], 200, null, false);
    }

    public function store(StoreContactInfoRequest $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title', 'sub_title', 'description', 'sub_description', 'address'],
                ['email', 'phone', 'phone_alt', 'whatsapp', 'facebook', 'instagram', 'youtube']
            );
            $contactInfo = ContactInfo::create($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $item) {
                    UploadImage($item, ContactInfo::PATH_IMAGE, ContactInfo::class, $contactInfo->id, false, null, Upload::IMAGE);
                }
            }
            return mainResponse(true, 'Test created successfully.', compact('contactInfo'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function update(UpdateContactInfoRequest $request, $id)
    {
        try {
            $contactInfo = ContactInfo::findOrFail($id);

            $data = localizedRequestData(
                $request,
                ['title', 'sub_title', 'description', 'sub_description', 'address'],
                ['email', 'phone', 'phone_alt', 'whatsapp', 'facebook', 'instagram', 'youtube']
            );
            $contactInfo->update($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $item) {
                    UploadImage($item, ContactInfo::PATH_IMAGE, ContactInfo::class, $contactInfo->id, false, null, Upload::IMAGE, null, $index == 0);
                }
            }

            return mainResponse(true, 'Test Updated successfully.', compact('contactInfo'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
}
