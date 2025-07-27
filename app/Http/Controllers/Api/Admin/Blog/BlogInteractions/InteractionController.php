<?php

namespace App\Http\Controllers\Api\Admin\Blog\BlogInteractions;

use App\Models\ArticleLike;
use App\Models\BlogArticle;
use Illuminate\Http\Request;
use App\Models\ArticleComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleComment;

class InteractionController extends Controller
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
                return mainResponse(true, 'Comment saved successfully.', ['comment' => $comment], [], 201, null, false);
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


    public function toggleLike(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:1,2',
                'likeable_id' => 'required|integer',
            ]);
            $likeableType = match ((int) $request->type) {
                1 => BlogArticle::class,
                2 => ArticleComment::class,
                default => null
            };
            if (!$likeableType) {
                return mainResponse(false, 'Invalid type.', [], [], 422);
            }
            $likeableId = $request->likeable_id;
            $isUser = auth('api')->check();
            $isAdmin = auth('admin')->check();
            if (!$isUser && !$isAdmin) {
                return mainResponse(false, 'Unauthorized.', [], [], 401);
            }
            $userId = $isUser ? auth('api')->id() : null;
            $adminId = $isAdmin ? auth('admin')->id() : null;
            $existingLike = ArticleLike::where('likeable_type', $likeableType)
                ->where('likeable_id', $likeableId)
                ->where('user_id', $userId)
                ->where('admin_id', $adminId)
                ->first();
            if ($existingLike) {
                $existingLike->delete();
                return mainResponse(true, 'Like removed successfully.', [], [], 201, null, false);
            }
            ArticleLike::create([
                'likeable_type' => $likeableType,
                'likeable_id' => $likeableId,
                'user_id' => $userId,
                'admin_id' => $adminId,
            ]);
            return mainResponse(true, 'Like added successfully.', [], [], 201, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500);
        }
    }
}
