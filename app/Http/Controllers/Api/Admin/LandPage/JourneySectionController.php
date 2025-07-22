<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use App\Models\Upload;
use Illuminate\Http\Request;
use App\Models\JourneyFeature;
use App\Models\JourneySection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JourneySectionController extends Controller
{
    public function store(Request $request)
    {
        try {
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['title_' . $key] = 'required|string|max:255';
                $rules['description_' . $key] = 'required|string';
            }
            $rules['images'] = 'required|array|size:4';
            $rules['images.*'] = 'required|file|mimes:jpeg,jpg,png';
            $rules['features'] = 'required|array|min:1|max:4';
            $rules['features.*'] = 'required|json';
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422);
            }
            $data = [];
            foreach (locales() as $key => $language) {
                $data['title'][$key] = $request->get('title_' . $key);
                $data['description'][$key] = $request->get('description_' . $key);
            }
            $journe = JourneySection::create($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $item) {
                    UploadImage($item, JourneySection::PATH_IMAGE, JourneySection::class, $journe->id, false, null, Upload::IMAGE);
                }
            }
            if ($request->has('features')) {
                foreach ($request->features as $featureJson) {
                    $feature = json_decode($featureJson, true);
                    if (is_array($feature)) {
                        $dataFeature = [];
                        foreach (locales() as $key => $language) {
                            $dataFeature[$key] = $feature[$key] ?? null;
                        }
                        if (!in_array(null, $dataFeature, true)) {
                            JourneyFeature::create([
                                'journey_section_id' => $journe->id,
                                'feature' => $dataFeature,
                            ]);
                        }
                    }
                }
            }
            return mainResponse(true, 'Journe section created successfully.', compact('journe'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
}
