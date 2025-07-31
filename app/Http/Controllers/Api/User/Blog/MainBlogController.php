<?php

namespace App\Http\Controllers\Api\User\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MainBlogController extends Controller
{

    public function getAllBlogSections($locale)
    {
        app()->setLocale($locale);
        $data = Cache::remember("all_blog_sections_{$locale}", now()->addMinutes(5), function () use ($locale) {
            $usedIds = [];
            // 1. hero
            $hero = BlogArticle::with([
                'images:id,path,relation_id,relation_type',
                'category',
                'keywords:id,name,section_id,section_type'
            ])
                ->where('status', 1)
                ->latest()
                ->first();
            $heroData = null;
            if ($hero) {
                $heroData = formatTranslatableData(
                    $hero,
                    ['title', 'slug', 'excerpt', 'meta_title', 'meta_description'],
                    ['id', 'blog_category_id', 'created_at'],
                    ['images' => 'images', 'category.name' => 'category']
                );
                $heroData['keywords'] = $hero->keywords->pluck('name')->toArray();
                $usedIds[] = $hero->id;
            }
            // 2. latest raw articles
            $latest_raw_articles = BlogArticle::with('images:id,path,relation_id,relation_type')
                ->where('status', 1)
                ->whereNotIn('id', $usedIds)
                ->latest()
                ->take(10)
                ->get();
            $latest_data = [];
            foreach ($latest_raw_articles as $article) {
                if (count($latest_data) >= 3) break;
                $latest_data[] = [
                    'id' => $article->id,
                    'title' => $article->getTranslation('title', $locale),
                    'slug' => $article->getTranslation('slug', $locale),
                    'created_at' => formatLocalizedDate($article->created_at, $locale),
                    'image' => optional($article->images->first())->path
                        ? asset('storage/' . $article->images->first()->path)
                        : null,
                ];
                $usedIds[] = $article->id;
            }
            // formatter
            $formatArticle = function ($article) use ($locale) {
                return [
                    'id' => $article->id,
                    'title' => $article->getTranslation('title', $locale),
                    'slug' => $article->getTranslation('slug', $locale),
                    'created_at' => formatLocalizedDate($article->created_at, $locale),
                    'image' => optional($article->images->first())->path
                        ? asset('storage/' . $article->images->first()->path)
                        : null,
                ];
            };
            // 3. tips
            $tips_data = BlogArticle::with('images:id,path,relation_id,relation_type')
                ->where('status', 1)
                ->where('blog_category_id', 1)
                ->whereNotIn('id', $usedIds)
                ->latest()
                ->take(4)
                ->get()
                ->map(function ($a) use (&$usedIds, $formatArticle) {
                    $usedIds[] = $a->id;
                    return $formatArticle($a);
                })
                ->toArray();
            // 4. stories
            $stories_data = BlogArticle::with('images:id,path,relation_id,relation_type')
                ->where('status', 1)
                ->where('blog_category_id', 2)
                ->whereNotIn('id', $usedIds)
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($a) use (&$usedIds, $formatArticle) {
                    $usedIds[] = $a->id;
                    return $formatArticle($a);
                })
                ->toArray();
            // 5. inspirations
            $inspirations_data = BlogArticle::with('images:id,path,relation_id,relation_type')
                ->where('status', 1)
                ->where('blog_category_id', 3)
                ->whereNotIn('id', $usedIds)
                ->latest()
                ->take(6)
                ->get()
                ->map(function ($a) use (&$usedIds, $formatArticle) {
                    $usedIds[] = $a->id;
                    return $formatArticle($a);
                })
                ->toArray();
            // استكمال النقص إن وجد
            $latest_to_remove = [];
            foreach ($latest_raw_articles as $article) {
                if (in_array($article->id, $usedIds)) continue;
                $formatted = $formatArticle($article);
                if ($article->blog_category_id == 1 && count($tips_data) < 4) {
                    $tips_data[] = $formatted;
                    $usedIds[] = $article->id;
                    $latest_to_remove[] = $article->id;
                }
                if ($article->blog_category_id == 2 && count($stories_data) < 3) {
                    $stories_data[] = $formatted;
                    $usedIds[] = $article->id;
                    $latest_to_remove[] = $article->id;
                }
                if ($article->blog_category_id == 3 && count($inspirations_data) < 6) {
                    $inspirations_data[] = $formatted;
                    $usedIds[] = $article->id;
                    $latest_to_remove[] = $article->id;
                }
            }
            $latest_data = array_filter($latest_data, function ($item) use ($latest_to_remove) {
                return !in_array($item['id'], $latest_to_remove);
            });
            $latest_data = array_values($latest_data);
            return [
                'hero' => $heroData,
                'latest' => $latest_data,
                'tips' => $tips_data,
                'stories' => $stories_data,
                'inspirations' => $inspirations_data,
            ];
        });
        return mainResponse(true, 'Fetched all blog sections successfully.', $data, [], 200, null, false);
    }


    public function detailsArticle($locale, $id)
    {
        app()->setLocale($locale);
        $userId = auth('sanctum')->check() ? auth('sanctum')->user()?->id : null;
        $adminId = auth('admin')->check() ? auth('admin')->user()?->id : null;
        $page = request('comment_page', 1);
        $article = BlogArticle::with([
            'images:id,path,relation_id,relation_type',
            'keywords:id,name,section_id,section_type',
            'category',
            'likes',
        ])
            ->where('status', 1)
            ->find($id);
        if (!$article) {
            return mainResponse(false, 'Article not found.', [], [], 404, null, false);
        }
        $articleData = formatTranslatableData(
            $article,
            ['title', 'slug', 'excerpt', 'content', 'meta_title', 'meta_description'],
            ['id', 'created_at'],
            ['images' => 'images', 'category.name' => 'category']
        );
        $articleData['keywords'] = $article->keywords->pluck('name')->toArray();
        $articleData['likes_count'] = $article->likes->count();
        $articleData['is_liked'] = $article->likes->contains(function ($like) use ($userId, $adminId) {
            return ($userId && $like->user_id === $userId) || ($adminId && $like->admin_id === $adminId);
        });
        $articleData['comments_count'] = $article->comments()->whereNull('parent_id')->count();
        $commentsPaginator = $article->comments()
            ->whereNull('parent_id')
            ->with([
                'replies.user:id,name',
                'replies.admin:id,name',
                'user:id,name',
                'admin:id,name',
                'likes',
            ])
            ->paginate(2, ['*'], 'comment_page', $page);
        $commentsPaginator->getCollection()->transform(function ($comment) use ($userId, $adminId) {
            return [
                'id' => $comment->id,
                'body' => $comment->body,
                'author' => $comment->user?->name ?? $comment->admin?->name ?? 'Unknown',
                'created_at' => $comment->created_at->toDateTimeString(),
                'likes_count' => $comment->likes->count(),
                'is_liked' => $comment->likes->contains(function ($like) use ($userId, $adminId) {
                    return ($userId && $like->user_id === $userId) || ($adminId && $like->admin_id === $adminId);
                }),
                'replies' => $comment->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'body' => $reply->body,
                        'author' => $reply->user?->name ?? $reply->admin?->name ?? 'Unknown',
                        'created_at' => $reply->created_at->toDateTimeString(),
                    ];
                }),
            ];
        });
        $latestArticles = BlogArticle::with('images:id,path,relation_id,relation_type')
            ->where('status', 1)
            ->where('id', '<>', $id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'created_at' => formatLocalizedDate($a->created_at, $locale),
                'title' => $a->getTranslation('title', $locale),
                'image' => optional($a->images->first())->path
                    ? asset('storage/' . $a->images->first()->path)
                    : null,
            ]);
        return mainResponse(true, 'Fetched article details successfully.', [
            'article' => $articleData,
            'comments' => $commentsPaginator,
            'latest_articles' => $latestArticles,
        ], [], 200);
    }

    public function filtreArticle($locale, $id, Request $request)
    {
        app()->setLocale($locale);

        // نعتبر {id} هو blog_category_id
        $request->merge(['blog_category_id' => $id]);

        $request->validate([
            'blog_category_id' => 'required|exists:blog_categories,id',
        ]);

        $articles = BlogArticle::withCount([
            'comments' => fn($q) => $q->whereNull('parent_id')
        ])
            ->with('images:id,path,relation_id,relation_type')
            ->where('status', 1)
            ->where('blog_category_id', $id)
            ->latest()
            ->paginate(6);

        // تجهيز كل مقال
        $articles->getCollection()->transform(function ($article) use ($locale) {
            return [
                'id' => $article->id,
                'title' => $article->getTranslation('title', $locale),
                'image' => optional($article->images->first())->path
                    ? asset('storage/' . $article->images->first()->path)
                    : null,
                'comments_count' => $article->comments_count,
                'created_at' => formatLocalizedDate($article->created_at, $locale),
            ];
        });

        return mainResponse(true, 'Article updated successfully.', compact('articles'), [], 200);
    }


    public function searchArticle($locale, Request $request)
{
    app()->setLocale($locale);

    $request->validate([
        'title' => 'required|string|min:2',
    ]);

    $query = $request->title;

    $articles = BlogArticle::withCount([
            'comments' => fn($q) => $q->whereNull('parent_id')
        ])
        ->with('images:id,path,relation_id,relation_type')
        ->where('status', 1)
        ->where("title->{$locale}", 'like', "%{$query}%")
        ->latest()
        ->paginate(6);

    $articles->getCollection()->transform(function ($article) use ($locale) {
        return [
            'id' => $article->id,
            'title' => $article->getTranslation('title', $locale),
            'image' => optional($article->images->first())->path
                ? asset('storage/' . $article->images->first()->path)
                : null,
            'comments_count' => $article->comments_count,
            'created_at' => formatLocalizedDate($article->created_at, $locale),
        ];
    });

            return mainResponse(true, 'Article updated successfully.', compact('articles'), [], 200);
}

}
