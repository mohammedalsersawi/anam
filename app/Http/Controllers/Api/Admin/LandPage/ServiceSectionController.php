<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use App\Models\Upload;
use App\Models\Feature;
use Illuminate\Http\Request;
use App\Models\ServiceSection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;

class ServiceSectionController extends Controller
{

    public function getData()
    {
        $sections = ServiceSection::with(['features', 'image'])->first();

        return mainResponse(true, 'Service sections fetched.', compact('sections'), [], 200, null, false);
    }


    public function store(StoreServiceRequest $request)
    {
        try {
            $title_ar = $request->input('title_ar') ?? $request->json('title_ar');
            $title_en = $request->input('title_en') ?? $request->json('title_en');
            $status   = $request->input('status') ?? $request->json('status');
            $items    = $request->input('items') ?? $request->json('items');
            $data = [
                'title' => [
                    'ar' => $title_ar,
                  'created_by' => auth('admin')->id(),
                  'en' => $title_en,
                ],
                'created_by' => auth('admin')->id(),
            ];
            $serviceSection = ServiceSection::create($data);
            if ($request->hasFile('image')) {
                UploadImage($request->file('image'), ServiceSection::PATH_IMAGE, ServiceSection::class, $serviceSection->id, true, null, Upload::IMAGE);
            }
            $features = [];
            foreach ($items ?? [] as $item) {
                $feature = Feature::create([
                    'section_id'   => $serviceSection->id,
                    'section_type' => ServiceSection::class,
                    'sub_title'    => $item['sub_title'] ?? [],
                    'description'  => $item['description'] ?? [],
                    'icon'         => $item['icon'] ?? null,
                    'button_text'  => $item['button_text'] ?? [],
                    'button_link'  => $item['button_link'] ?? [],
                    'created_by'   => auth('admin')->id(),
                ]);

                $features[] = $feature;
            }
            return mainResponse(
                true,
                'Service section created successfully.',
                [
                    'serviceSection' => $serviceSection,
                    'features' => $features,
                ],
                [],
                201,
                null,
                false
            );
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to store service section.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $serviceSection = ServiceSection::findOrFail($id);
            $title_ar = $request->input('title_ar') ?? $request->json('title_ar');
            $title_en = $request->input('title_en') ?? $request->json('title_en');
            $status   = $request->input('status') ?? $request->json('status');
            $items    = $request->input('items') ?? $request->json('items');
            $serviceSection->update([
               'title' => [
                    'ar' => $title_ar,
                  'created_by' => auth('admin')->id(),
                  'en' => $title_en,
                ],
                'updated_by' => auth('admin')->id(),
            ]);
            if ($request->hasFile('image')) {
                UploadImage($request->file('image'), ServiceSection::PATH_IMAGE, ServiceSection::class, $serviceSection->id, true, null, Upload::IMAGE);
            }
            Feature::where('section_id', $serviceSection->id)
                ->where('section_type', ServiceSection::class)
                ->delete();
            $features = [];
            foreach ($items ?? [] as $item) {
                $feature = Feature::create([
                    'section_id'   => $serviceSection->id,
                    'section_type' => ServiceSection::class,
                    'sub_title'    => $item['sub_title'] ?? [],
                    'description'  => $item['description'] ?? [],
                    'icon'         => $item['icon'] ?? null,
                    'button_text'  => $item['button_text'] ?? [],
                    'button_link'  => $item['button_link'] ?? [],
                    'updated_by'   => auth('admin')->id(),
                ]);
                $features[] = $feature;
            }

            return mainResponse(
                true,
                'Service section updated successfully.',
                [
                    'serviceSection' => $serviceSection,
                    'features' => $features
                ],
                [],
                200
            );
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to store service section.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
}
