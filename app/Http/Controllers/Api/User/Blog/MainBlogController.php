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
            $hero = BlogArticle::with(['images:id,path,relation_id,relation_type', 'category'])
                ->select('id', 'title', 'slug', 'excerpt', 'meta_title', 'meta_description', 'created_at', 'blog_category_id')
                ->where('status', 1)
                ->latest()
                ->first();
            $heroData = $hero ? formatTranslatableData(
                $hero,
                ['title', 'slug', 'excerpt', 'meta_title', 'meta_description'],
                ['id', 'blog_category_id', 'created_at'],
                ['images' => 'images', 'category.name' => 'category']
            ) : null;
            $articles = BlogArticle::with('images:id,path,relation_id,relation_type')
                ->select('id', 'title', 'slug', 'created_at', 'blog_category_id')
                ->where('status', 1)
                ->latest()
                ->take(20)
                ->get();
            $grouped = [
                'tips' => [],
                'stories' => [],
                'inspirations' => [],
                'latest' => [],
            ];
            foreach ($articles as $article) {
                $item = [
                    'title' => $article->getTranslation('title', $locale),
                    'slug' => $article->getTranslation('slug', $locale),
                    'created_at' => formatLocalizedDate($article->created_at, $locale),
                    'image' => optional($article->images->first())->path
                        ? asset('storage/' . $article->images->first()->path)
                        : null,
                ];
                if (count($grouped['latest']) < 5) {
                    $grouped['latest'][] = $item;
                }
                if ($article->blog_category_id == 1 && count($grouped['tips']) < 4) {
                    $grouped['tips'][] = $item;
                }
                if ($article->blog_category_id == 2 && count($grouped['stories']) < 3) {
                    $grouped['stories'][] = $item;
                }
                if ($article->blog_category_id == 3 && count($grouped['inspirations']) < 6) {
                    $grouped['inspirations'][] = $item;
                }
            }
            return [
                'hero' => $heroData,
                'groups' => $grouped,
            ];
        });
        return mainResponse(true, 'Fetched all blog sections successfully.', $data, [], 200, null, false);
    }


    public function detailsArticle($locale, $id)
    {
        app()->setLocale($locale);
        $article = BlogArticle::with(['images:id,path,relation_id,relation_type', 'category'])
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
        $latestArticles = BlogArticle::with('images:id,path,relation_id,relation_type')
            ->where('status', 1)
            ->where('id', '<>', $id)
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($a) => [
                'title' => $a->getTranslation('title', $locale),
                'image' => optional($a->images)->path
                    ? asset('storage/' . $a->images->path)
                    : null,
            ]);
        return mainResponse(true, 'Fetched article details successfully.', [
            'article' => $articleData,
            'latest_articles' => $latestArticles,
        ], [], 200, null, false);
    }
}
