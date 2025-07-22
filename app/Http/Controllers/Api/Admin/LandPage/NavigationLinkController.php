<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use Illuminate\Http\Request;
use App\Models\NavigationLink;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NavigationLinkController extends Controller
{

    public function getData()
    {
        $links = NavigationLink::orderBy('order')->get();
        return mainResponse(true, 'done', compact('links'), [], 200);
    }



    public function store(Request $request)
    {
        $rules = collect(locales())->mapWithKeys(function ($value, $key) {
            return ['label_' . $key => 'required|string|max:50']; // label_en, label_ar
        })->merge([
            'url' => 'required|url|max:100',
            'position' => 'required|in:0,1',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ])->all();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422);
        }
        $data = [
            'label' => collect(locales())->mapWithKeys(function ($value, $key) use ($request) {
                return [$key => $request->get('label_' . $key)];
            }),
            'url' => $request->url,
            'position' => $request->position,
            'order' => $request->filled('order')
                ? $request->order
                : NavigationLink::where('position', $request->position)->max('order') + 1,
            'status' => $request->status ?? 1,
        ];
        $link = NavigationLink::create($data);
        return mainResponse(true, 'Navigation link created successfully.', compact('link'), [], 201);
    }


    public function update(Request $request, $id)
    {

        $link = NavigationLink::findOrFail($id);
        $rules = collect(locales())->mapWithKeys(function ($value, $key) {
            return ['label_' . $key => 'required|string|max:50']; // label_en, label_ar
        })->merge([
            'url' => 'required|url|max:100',
            'position' => 'required|in:0,1',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
        ])->all();
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422);
        }
        $data = [
            'label' => collect(locales())->mapWithKeys(function ($value, $key) use ($request) {
                return [$key => $request->get('label_' . $key)];
            }),
            'url' => $request->url,
            'position' => $request->position,
            'order' => $request->filled('order') ? $request->order : $link->order,
            'status' => $request->status ?? $link->status,
        ];
        $link->update($data);
        return mainResponse(true, 'Navigation link updated successfully.', compact('link'), [], 200);
    }

    public function destroy($id)
    {
        $link = NavigationLink::findOrFail($id);
        $link->delete();
        return mainResponse(true, 'Navigation link deleted successfully.', [], [], 200);
    }

    public function updateStatus($id)
    {
        try {
            $link = NavigationLink::findOrFail($id);
            $link->update(['status' => !$link->status]);
            return mainResponse(true,'Link status toggled successfully',
                ['new_status' => $link->fresh()->status,'link_id' => $id],[],200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to toggle status: ' . $e->getMessage(), [], [], 500);
        }
    }
}
