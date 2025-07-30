<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use App\Models\Upload;
use App\Models\Feature;
use Illuminate\Http\Request;
use App\Models\FeatureSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;

class FeatureController extends Controller
{

    public function getData()
    {
        $sections = FeatureSection::with(['features', 'image'])->first();

        return mainResponse(true, 'Feature sections fetched.', compact('sections'), [], 200, null, false);
    }
    public function store(Request $request)
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
            $featureSection = FeatureSection::create($data);
            if ($request->hasFile('image')) {
                UploadImage($request->file('image'), FeatureSection::PATH_IMAGE, FeatureSection::class, $featureSection->id, true, null, Upload::IMAGE);
            }
            foreach ($items ?? [] as $item) {
                Feature::create([
                    'section_id'   => $featureSection->id,
                    'section_type' => FeatureSection::class,
                    'sub_title'    => $item['sub_title'] ?? [],
                    'description'  => $item['description'] ?? [],
                    'icon'         => $item['icon'] ?? null,
                    'button_text'  => $item['button_text'] ?? [],
                    'created_by'  => auth('admin')->id(),
                ]);
            }
            return mainResponse(true, 'Service section created successfully.', compact('featureSection'), [], 201);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to store service section.', [], ['server' => [$e->getMessage()]], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            // جلب السجل القديم
            $featureSection = FeatureSection::findOrFail($id);

            // التقاط البيانات من form-data أو JSON
            $title_ar = $request->input('title_ar') ?? $request->json('title_ar');
            $title_en = $request->input('title_en') ?? $request->json('title_en');
            $status   = $request->input('status') ?? $request->json('status');
            $items    = $request->input('items') ?? $request->json('items');
            $featureSection->update([
                'title' => [
                    'ar' => $title_ar,
                    'created_by' => auth('admin')->id(),
                    'en' => $title_en,
                ],
                'updated_by' => auth('admin')->id(),
            ]);
            if ($request->hasFile('image')) {
                UploadImage($request->file('image'), FeatureSection::PATH_IMAGE, FeatureSection::class, $featureSection->id, true, null, Upload::IMAGE);
            }
            Feature::where('section_id', $featureSection->id)
                ->where('section_type', FeatureSection::class)
                ->delete();

            // إعادة إنشاء العناصر الجديدة
            foreach ($items ?? [] as $item) {
                Feature::create([
                    'section_id'   => $featureSection->id,
                    'section_type' => FeatureSection::class,
                    'sub_title'    => $item['sub_title'] ?? [],
                    'description'  => $item['description'] ?? [],
                    'icon'         => $item['icon'] ?? null,
                    'updated_by'  => auth('admin')->id(),
                ]);
            }
            return mainResponse(true, 'Feature section updated successfully.', compact('featureSection'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to update feature section.', [], ['server' => [$e->getMessage()]], 500);
        }
    }
}
