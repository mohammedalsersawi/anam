<?php

namespace App\Http\Controllers\Api\Admin\LandPage;

use App\Models\Upload;
use App\Models\HeroSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HeroSectionController extends Controller
{

    public function index()
    {
        try {
            $heroes = HeroSection::with('imageHero')
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            if ($heroes->isEmpty()) {
                return mainResponse(false, 'No hero sections found.', [], [], 404, null, false);
            }
            return mainResponse(true, 'Hero sections fetched successfully.', compact('heroes'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch hero sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
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
            $rules['status'] = 'required|in:0,1';
            $rules['image'] = 'required|mimes:jpeg,jpg,png';

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
            $hero = HeroSection::create($data);
            if ($request->has('image')) {
                UploadImage($request->image, HeroSection::PATH_IMAGE, HeroSection::class, $hero->id, true, null, Upload::IMAGE);
            }
            return mainResponse(true, 'Hero section created successfully.', compact('hero'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $hero = HeroSection::with('imageHero')->findOrFail($id);
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['title_' . $key] = 'required|string|max:255';
                $rules['description_' . $key] = 'required|string';
                $rules['button_text_' . $key] = 'required|string|max:100';
            }
            $rules['button_link'] = 'required|string|max:255';
            $rules['status'] = 'required|in:0,1';
            $rules['image'] = 'nullable|mimes:jpeg,jpg,png';

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
            $data['status'] = $request->status;

            $hero->update($data);
            if ($request->hasFile('image')) {
                UploadImage($request->image, HeroSection::PATH_IMAGE, HeroSection::class, $hero->id, true, null, Upload::IMAGE);
            }
            $hero->load('imageHero');

            return mainResponse(true, 'Hero section updated successfully.', compact('hero'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function destroy($id)
    {
        $hero = HeroSection::findOrFail($id);

        if ($hero->imageHero && $hero->imageHero->path) {
            $path = $hero->imageHero->path;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $hero->imageHero->delete();
        }
        $hero->delete();
        return mainResponse(true, 'Hero Section deleted successfully.', [], [], 200);
    }

    public function updateStatus($id)
    {
        try {
            $link = HeroSection::findOrFail($id);
            $link->update(['status' => !$link->status]);
            return mainResponse(
                true,
                'Link status toggled successfully',
                ['new_status' => $link->fresh()->status, 'link_id' => $id],
                [],
                200
            );
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to toggle status: ' . $e->getMessage(), [], [], 500);
        }
    }
}




    // public function getData()
    // {
    //     $hero = HeroSection::with('imageHero')
    //         ->where('status', 1)
    //         ->latest()
    //         ->first();

    //     return mainResponse(true, 'done', compact('hero'), [], 200, null, false);
    // }
