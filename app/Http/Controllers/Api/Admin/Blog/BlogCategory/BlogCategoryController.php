<?php

namespace App\Http\Controllers\Api\Admin\Blog\BlogCategory;

use App\Models\Upload;
use App\Models\BlogCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogCategory;

class BlogCategoryController extends Controller
{
    public function index()
    {
        try {
            $tests = BlogCategory::orderBy('created_at', 'desc')
                ->paginate(10);

            if ($tests->isEmpty()) {
                return mainResponse(false, 'No active services found.', [], [], 404, null, false);
            }

            return mainResponse(true, 'Fetched BlogCategory sections successfully.', compact('tests'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(StoreBlogCategory $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['name', 'description'],
                []
            );
            if (empty($data['slug']) || !is_array($data['slug'])) {
                $data['slug'] = generateLocalizedSlugs($data['name']);
            }
            $blogCategory = BlogCategory::create($data);
            return mainResponse(true, 'BlogCategory created successfully.', compact('blogCategory'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500);
        }
    }



    public function update(StoreBlogCategory $request, $id)
    {
        try {
            $blogCategory = BlogCategory::findOrFail($id);

            $data = localizedRequestData(
                $request,
                ['name', 'description'],
                []
            );
            if (empty($data['slug']) || !is_array($data['slug'])) {
                $data['slug'] = generateLocalizedSlugs($data['name']);
            }
            $blogCategory->update($data);
            return mainResponse(true, 'BlogCategory updated successfully.', compact('blogCategory'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $blogCategory = BlogCategory::find($id);
            if (!$blogCategory) {
                return mainResponse(false, 'Test section not found.', [], [], 404, null, false);
            }
            $blogCategory->delete();
            return mainResponse(true, 'BlogCategory section deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(BlogCategory::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }
}
