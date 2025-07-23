<?php

namespace App\Http\Controllers\Api\Admin\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::orderBy('created_at', 'desc')
                ->paginate(10);

            if ($categories->isEmpty()) {
                return mainResponse(false, 'No active services found.', [], [], 404, null, false);
            }

            return mainResponse(true, 'Fetched category sections successfully.', compact('services'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(Request $request)
    {
        try {
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['name_' . $key] = 'required|string|max:255';
            }
            $rules['type'] = 'required|in:1,2,3';
            $rules['status'] = 'required|in:0,1';
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422, null, false);
            }
            $data = [];
            foreach (locales() as $key => $language) {
                $data['name'][$key] = $request->get('name_' . $key);
            }
            $data['type'] = $request->type;
            $data['status'] = $request->status;
            $category = Category::create($data);
            return mainResponse(true, 'Category section created successfully.', compact('category'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $rules = [];
            foreach (locales() as $key => $language) {
                $rules['name_' . $key] = 'required|string|max:255';
            }
            $rules['type'] = 'required|in:1,2,3';
            $rules['status'] = 'required|in:0,1';
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 422, null, false);
            }
            $category = Category::findOrFail($id);
            $data = [];
            foreach (locales() as $key => $language) {
                $data['name'][$key] = $request->get('name_' . $key);
            }
            $data['type'] = $request->type;
            $data['status'] = $request->status;
            $category->update($data);
            return mainResponse(true, 'Service section updated successfully.', compact('category'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return mainResponse(false, 'Category section not found.', [], [], 404, null, false);
            }
            $category->delete();
            return mainResponse(true, 'Category section deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }



    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(Category::class, $id, 'status');
        $newStatus = data_get($result, 'data.new_status');
        if ($result['success'] && $newStatus === 0) {
            $category = Category::find($id);
            if ($category) {
                switch ($category->type) {
                    case 1:
                        \App\Models\Test::where('category_id', $category->id)->update(['status' => 0]);
                        break;
                    case 2:
                        \App\Models\Course::where('category_id', $category->id)->update(['status' => 0]);
                        break;
                }
            }
        }
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }
}
