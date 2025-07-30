<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use Illuminate\Http\Request;
use App\Models\ServiceSection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ServiceSectionController extends Controller
{

    public function index()
    {
        try {
            $services = ServiceSection::orderBy('created_at', 'desc')
                ->paginate(10);

            if ($services->isEmpty()) {
                return mainResponse(false, 'No active services found.', [], [], 404, null, false);
            }

            return mainResponse(true, 'Fetched service sections successfully.', compact('services'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function store(Request $request)
    {
        try {
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['title_' . $key] = 'required|string|max:255';
                $rules['description_' . $key] = 'required|string';
                $rules['button_text_' . $key] = 'required|string|max:100';
            }
            $rules['button_link'] = 'required|string|max:255';
            $rules['icon'] = 'required|string|max:50';
            $rules['status'] = 'required|in:0,1';
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422, null, false);
            }
            $data = [];
            foreach (locales() as $key => $language) {
                $data['title'][$key] = $request->get('title_' . $key);
                $data['description'][$key] = $request->get('description_' . $key);
                $data['button_text'][$key] = $request->get('button_text_' . $key);
            }
            $data['button_link'] = $request->button_link;
            $data['icon'] = $request->icon;
            $service = ServiceSection::create($data);
            return mainResponse(true, 'Service section created successfully.', compact('service'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['title_' . $key] = 'required|string|max:255';
                $rules['description_' . $key] = 'required|string';
                $rules['button_text_' . $key] = 'required|string|max:100';
            }
            $rules['button_link'] = 'required|string|max:255';
            $rules['icon'] = 'required|string|max:50';
            $rules['status'] = 'required|in:0,1';
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422, null, false);
            }
            $service = ServiceSection::findOrFail($id);
            $data = [];
            foreach (locales() as $key => $language) {
                $data['title'][$key] = $request->get('title_' . $key);
                $data['description'][$key] = $request->get('description_' . $key);
                $data['button_text'][$key] = $request->get('button_text_' . $key);
            }
            $data['button_link'] = $request->button_link;
            $data['icon'] = $request->icon;
            $data['status'] = $request->status;
            $service->update($data);
            return mainResponse(true, 'Service section updated successfully.', compact('service'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function destroy($id)
    {
        try {
            $service = ServiceSection::find($id);
            if (!$service) {
                return mainResponse(false, 'Service section not found.', [], [], 404, null, false);
            }
            $service->delete();
            return mainResponse(true, 'Service section deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(ServiceSection::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }
}



 // public function getData()
    // {
    //     try {
    //         $services = ServiceSection::where('status', 1)
    //             ->orderBy('created_at', 'desc')
    //             ->take(4)
    //             ->get();

    //         return mainResponse(true, 'Fetched service sections successfully.', compact('services'), [], 200, null, false);
    //     } catch (\Exception $e) {
    //         return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500);
    //     }
    // }
    // public function store1(StoreServiceRequest $request)
    // {
    //     try {
    //         $title_ar = $request->input('title_ar') ?? $request->json('title_ar');
    //         $title_en = $request->input('title_en') ?? $request->json('title_en');
    //         $status   = $request->input('status') ?? $request->json('status');
    //         $items    = $request->input('items') ?? $request->json('items');
    //         $data = [
    //             'title' => [
    //                 'ar' => $title_ar,
    //                 'en' => $title_en,
    //             ],
    //         ];
    //         $data['created_by'] =  auth('admin')->id();
    //         $serviceSection = ServiceSection::create($data);
    //         if ($request->hasFile('image')) {
    //             UploadImage($request->file('image'), ServiceSection::PATH_IMAGE, ServiceSection::class, $serviceSection->id, true, null, Upload::IMAGE);
    //         }
    //         foreach ($items ?? [] as $item) {
    //             Feature::create([
    //                 'section_id'   => $serviceSection->id,
    //                 'section_type' => ServiceSection::class,
    //                 'sub_title'    => $item['sub_title'] ?? [],
    //                 'description'  => $item['description'] ?? [],
    //                 'icon'         => $item['icon'] ?? null,
    //                 'button_text'  => $item['button_text'] ?? [],
    //                 'button_link'  => $item['button_link'] ?? [],
    //                 'created_by'  => auth('admin')->id(),
    //             ]);
    //         }
    //         return mainResponse(true, 'Service section created successfully.', compact('serviceSection'), [], 201, null, false);
    //     } catch (\Exception $e) {
    //         return mainResponse(false, 'Failed to store service section.', [], ['server' => [$e->getMessage()]], 500, null, false);
    //     }
    // }




    //  public function updat1e(UpdateServiceRequest $request, $id)
    // {
    //     try {
    //         // جلب السجل القديم
    //         $serviceSection = ServiceSection::findOrFail($id);
    //         $title_ar = $request->input('title_ar') ?? $request->json('title_ar');
    //         $title_en = $request->input('title_en') ?? $request->json('title_en');
    //         $status   = $request->input('status') ?? $request->json('status');
    //         $items    = $request->input('items') ?? $request->json('items');
    //         $serviceSection->update([
    //             'title' => [
    //                 'ar' => $title_ar,
    //                 'en' => $title_en,
    //             ],
    //         ]);
    //         if ($request->hasFile('image')) {
    //             UploadImage($request->file('image'), ServiceSection::PATH_IMAGE, ServiceSection::class, $serviceSection->id, true, null, Upload::IMAGE);
    //         }
    //         Feature::where('section_id', $serviceSection->id)
    //             ->where('section_type', ServiceSection::class)
    //             ->delete();
    //         foreach ($items ?? [] as $item) {
    //             Feature::create([
    //                 'section_id'   => $serviceSection->id,
    //                 'section_type' => ServiceSection::class,
    //                 'sub_title'    => $item['sub_title'] ?? [],
    //                 'description'  => $item['description'] ?? [],
    //                 'icon'         => $item['icon'] ?? null,
    //                 'button_text'  => $item['button_text'] ?? [],
    //                 'button_link'  => $item['button_link'] ?? [],
    //                 'created_by'  => auth('admin')->id(),
    //             ]);
    //         }
    //         return mainResponse(true, 'Service section updated successfully.', compact('serviceSection'), [], 200);
    //     } catch (\Exception $e) {
    //         return mainResponse(false, 'Failed to update service section.', [], ['server' => [$e->getMessage()]], 500);
    //     }
    // }
