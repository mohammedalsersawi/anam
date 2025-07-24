<?php

namespace App\Http\Controllers\Api\User;

use App\Models\HeroSection;
use Illuminate\Http\Request;
use App\Models\JourneySection;
use App\Models\ServiceSection;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FeatureSection;
use App\Models\SuccessStorie;
use App\Models\Test;

class MainController extends Controller
{
    public function heroSection($locale)
    {
        app()->setLocale($locale);

        $heroSection = HeroSection::with('imageHero')
            ->where('status', 1)
            ->latest()
            ->first();

        if (!$heroSection) {
            return mainResponse(false, 'No active services found.', [], [], 404, null, false);
        }
        $data = formatTranslatableData(
            $heroSection,
            ['title', 'description', 'button_text'],
            ['id', 'button_link'],
            ['imageHero' => 'image']
        );
        return mainResponse(true, 'Fetched Hero sections successfully.', $data, [], 200, null, false);
    }


   public function SuccessStorie($locale)
{
    app()->setLocale($locale);

    $story = SuccessStorie::with('images')
        ->latest()
        ->first();

    if (!$story) {
        return mainResponse(false, 'No active courses found.', [], [], 404, null, false);
    }

    $data = formatTranslatableData(
        $story,
        ['title'],
        ['id' , 'url_video'],
        ['images' => 'images']
    );

    return mainResponse(true, 'Fetched course section successfully.', ['SuccessStory' => $data], [], 200, null, false);
}




    public function courses($locale)
    {
        app()->setLocale($locale);

        $tests = Course::with('image')
            ->where('status', 1)
            ->latest()
            ->take(3)
            ->get();
        if ($tests->isEmpty()) {
            return mainResponse(false, 'No active courses found.', [], [], 404, null, false);
        }
        $data = $tests->map(function ($test) {
            return formatTranslatableData(
                $test,
                ['title', 'description'],
                ['id'],
                ['image' => 'image']
            );
        });
        return mainResponse(true, 'Fetched courses sections successfully.', $data, [], 200, null, false);
    }


    public function tsets($locale)
    {
        app()->setLocale($locale);

        $tests = Course::with('image')
            ->where('status', 1)
            ->latest()
            ->take(3)
            ->get();
        if ($tests->isEmpty()) {
            return mainResponse(false, 'No active services found.', [], [], 404, null, false);
        }
        $data = $tests->map(function ($test) {
            return formatTranslatableData(
                $test,
                ['title', 'description'],
                ['id'],
                ['image' => 'image']
            );
        });
        return mainResponse(true, 'Fetched Hero sections successfully.', $data, [], 200, null, false);
    }

























    public function FeatureSection($locale)
    {
        app()->setLocale($locale);
        $ServiceSection = FeatureSection::with(['image', 'features'])
            ->latest()
            ->first();
        if (!$ServiceSection) {
            return mainResponse(false, 'No active services found.', [], [], 404, null, false);
        }
        $data = formatTranslatableData(
            $ServiceSection,
            ['title'],
            ['id'],
            ['image' => 'image', 'features' => 'features']
        );
        return mainResponse(true, 'Fetched Hero sections successfully.', ['JourneySection' => $data], [], 200, null, false);
    }

    public function ServiceSection($locale)
    {
        app()->setLocale($locale);
        $ServiceSection = ServiceSection::with(['image', 'features'])
            ->latest()
            ->first();
        if (!$ServiceSection) {
            return mainResponse(false, 'No active services found.', [], [], 404, null, false);
        }
        $data = formatTranslatableData(
            $ServiceSection,
            ['title'],
            ['id'],
            ['image' => 'image', 'features' => 'features']
        );
        return mainResponse(true, 'Fetched Hero sections successfully.', ['JourneySection' => $data], [], 200, null, false);
    }


    public function journeySection($locale)
    {
        app()->setLocale($locale);
        $heroSection = JourneySection::with(['images', 'features'])
            ->latest()
            ->first();
        if (!$heroSection) {
            return mainResponse(false, 'No active services found.', [], [], 404, null, false);
        }
        $data = formatTranslatableData(
            $heroSection,
            ['title', 'description'],
            ['id'],
            ['images' => 'images', 'features' => 'features']
        );
        return mainResponse(true, 'Fetched Hero sections successfully.', ['JourneySection' => $data], [], 200, null, false);
    }
}
