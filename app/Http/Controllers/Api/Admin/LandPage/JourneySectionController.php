<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use App\Models\Upload;
use Illuminate\Http\Request;
use App\Models\JourneyFeature;
use App\Models\JourneySection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JourneySectionController extends Controller
{

    public function index()
    {
        try {
            $journeys = JourneySection::with(['features' ,'images'])
                ->latest()
                ->paginate(10);

            if ($journeys->isEmpty()) {
                return mainResponse(false, 'No journey sections found.', [], [], 404, null, false);
            }

            return mainResponse(true, 'Data fetched successfully.', compact('journeys'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }




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


    public function update(Request $request, $id)
    {
        try {
            $journe = JourneySection::findOrFail($id);
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['title_' . $key] = 'required|string|max:255';
                $rules['description_' . $key] = 'required|string';
            }
            $rules['images'] = 'nullable|array|size:4';
            $rules['images.*'] = 'nullable|file|mimes:jpeg,jpg,png';
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
            $journe->update($data);
            if ($request->hasFile('images')) {
                $oldImages = Upload::where('relation_id', $journe->id)
                    ->where('relation_type', JourneySection::class)
                    ->where('type', Upload::IMAGE)
                    ->get();
                foreach ($oldImages as $img) {
                    if (Storage::disk('public')->exists($img->path)) {
                        Storage::disk('public')->delete($img->path);
                    }
                    $img->delete();
                }
                foreach ($request->file('images') as $item) {
                    UploadImage($item, JourneySection::PATH_IMAGE, JourneySection::class, $journe->id, false, null, Upload::IMAGE);
                }
            }
            JourneyFeature::where('journey_section_id', $journe->id)->delete();
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
            return mainResponse(true, 'Journey section updated successfully.', compact('journe'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }




    public function destroy($id)
    {
        $journe = JourneySection::find($id);
        if (!$journe) {
            return mainResponse(false, 'The requested section does not exist.', [], [], 404);
        }
        if ($journe->images && $journe->images->count()) {
            foreach ($journe->images as $image) {
                if ($image->path && Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }
        }
        if ($journe->features && $journe->features->count()) {
            foreach ($journe->features as $feature) {
                $feature->delete();
            }
        }
        $journe->delete();
        return mainResponse(true, 'Journey Section deleted successfully.', [], [], 200);
    }
}



// public function getData()
//     {
//         try {
//             $journe = JourneySection::with(['features', 'images'])
//                 ->latest()
//                 ->first();

//             if (!$journe) {
//                 return mainResponse(false, 'There is no section yet.', [], [], 404, null, false);
//             }

//             return mainResponse(true, 'Data fetched successfully.', compact('journe'), [], 200, null, false);
//         } catch (\Exception $e) {
//             return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
//         }
//     }
