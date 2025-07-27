<?php

namespace App\Http\Controllers\Api\Admin\Blog\BlogInteractions;

use Illuminate\Http\Request;
use App\Models\ArticleComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleComment;

class ArticleCommentController extends Controller
{
    public function store(StoreArticleComment $request)
    {
        try {
            $data = $request->validated();

            if ($data['type'] === '1') {
                $comment = ArticleComment::create([
                    'body' => $data['body'],
                    'blog_article_id' => $data['blog_article_id'],
                    'user_id' => auth('api')->id(),
                    'admin_id' => null,
                    'parent_id' => null,
                ]);
                return mainResponse(true, 'Comment saved successfully.', ['comment' => $comment], [], 201 ,null,false);
            }
            if ($data['type'] === '2') {
                $reply = ArticleComment::create([
                    'body' => $data['body'],
                    'blog_article_id' => $data['blog_article_id'],
                    'user_id' => null,
                    'admin_id' => auth('admin')->id(),
                    'parent_id' => $data['parent_id'],
                ]);
                return mainResponse(true, 'Reply saved successfully.', ['reply' => $reply], [], 201);
            }
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500);
        }
    }
}
