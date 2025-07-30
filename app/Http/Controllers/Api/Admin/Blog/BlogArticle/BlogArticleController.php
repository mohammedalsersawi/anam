<?php

namespace App\Http\Controllers\Api\Admin\Blog\BlogArticle;

use App\Models\Upload;
use App\Models\BlogArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogArticle;
use App\Http\Requests\UpdateBlogArticle;

class BlogArticleController extends Controller
{

    public function index()
    {
        try {
            $article = BlogArticle::orderBy('created_at', 'desc')
                ->paginate(10);

            if ($article->isEmpty()) {
                return mainResponse(false, 'No active article found.', [], [], 404, null, false);
            }

            return mainResponse(true, 'Fetched article sections successfully.', compact('article'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Failed to fetch service sections.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function store(StoreBlogArticle $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title', 'excerpt', 'content', 'meta_title', 'meta_description'],
                ['status', 'blog_category_id']
            );
            if (empty($data['slug']) || !is_array($data['slug'])) {
                $data['slug'] = generateLocalizedSlugs($data['title']);
            }
            $data['created_by'] = auth('admin')->id();
            $article = BlogArticle::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $item) {
                    UploadImage($item, BlogArticle::PATH_IMAGE, BlogArticle::class, $article->id, false, null, Upload::IMAGE);
                }
            }
            if ($request->filled('keywords')) {
                foreach ($request->keywords as $keywordData) {
                    add_keyword($keywordData, $article->id, BlogArticle::class, auth('admin')->id());
                }
            }
            return mainResponse(true, 'Article created successfully.', compact('article'), [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500);
        }
    }

    public function update(UpdateBlogArticle $request, $id)
    {
        try {
            $article = BlogArticle::findOrFail($id);

            $data = localizedRequestData(
                $request,
                ['title', 'excerpt', 'content', 'meta_title', 'meta_description'],
                ['status', 'blog_category_id']
            );
            if (empty($data['slug']) || !is_array($data['slug'])) {
                $data['slug'] = generateLocalizedSlugs($data['title']);
            }
            $data['updated_by'] = auth('admin')->id();
            $article->update($data);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $item) {
                    UploadImage($item, BlogArticle::PATH_IMAGE, BlogArticle::class, $article->id, false, null, Upload::IMAGE, null, $index == 0);
                }
            }
            if ($request->filled('keywords')) {
                sync_keywords($request->keywords, $article->id, \App\Models\BlogArticle::class, auth('admin')->id());
            }
            return mainResponse(true, 'Article updated successfully.', compact('article'), [], 200);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $article = BlogArticle::find($id);
            if (!$article) {
                return mainResponse(false, 'Article  not found.', [], [], 404, null, false);
            }
            $article->delete();
            return mainResponse(true, 'Article  deleted successfully.', [], [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
    public function updateStatus($id)
    {
        $result = toggleModelBooleanField(BlogArticle::class, $id, 'status');
        return mainResponse($result['success'], $result['message'], $result['data'] ?? [], $result['errors'] ?? [], $result['status'], null, false);
    }
}
