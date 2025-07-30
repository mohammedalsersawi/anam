<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Test;
use App\Models\Course;
use App\Models\ContactInfo;
use App\Models\HeroSection;
use Illuminate\Http\Request;
use App\Models\SuccessStorie;
use App\Models\FeatureSection;
use App\Models\JourneySection;
use App\Models\ServiceSection;
use App\Http\Controllers\Controller;

class MainController extends Controller
{



    public function homepageContent($locale)
    {
        app()->setLocale($locale);

        //  Hero Section
        $hero = HeroSection::with('imageHero')
            ->where('status', 1)
            ->latest()
            ->first();
        $heroData = $hero
            ? formatTranslatableData(
                $hero,
                ['title', 'description', 'button_text'],
                ['id', 'button_link'],
                ['imageHero' => 'image']
            )
            : null;

        //  Journey Section
        $journey = JourneySection::with(['images', 'features'])->latest()->first();
        $journeyData = $journey
            ? formatTranslatableData(
                $journey,
                ['title', 'description'],
                ['id'],
                ['images' => 'images', 'features' => 'features']
            )
            : null;

        //  Service Section
        $service = ServiceSection::with(['image', 'features'])->latest()->first();
        $serviceData = $service
            ? formatTranslatableData(
                $service,
                ['title'],
                ['id'],
                ['image' => 'image', 'features' => 'features']
            )
            : null;

        //  Tsets Section (من جدول Test)
        $tsets = Test::with('image')->where('status', 1)->latest()->take(3)->get();
        $tsetsData = !$tsets->isEmpty()
            ? $tsets->map(function ($test) {
                return formatTranslatableData(
                    $test,
                    ['title', 'description'],
                    ['id'],
                    ['image' => 'image']
                );
            })
            : null;

        //  Courses Section
        $courses = Course::with('image')->where('status', 1)->latest()->take(3)->get();
        $coursesData = !$courses->isEmpty()
            ? $courses->map(function ($course) {
                return formatTranslatableData(
                    $course,
                    ['title', 'description'],
                    ['id'],
                    ['image' => 'image']
                );
            })
            : null;

        //  Feature Section
        $feature = FeatureSection::with(['image', 'features'])->latest()->first();
        $featureData = $feature
            ? formatTranslatableData(
                $feature,
                ['title'],
                ['id'],
                ['image' => 'image', 'features' => 'features']
            )
            : null;

        //  Success Story Section
        $story = SuccessStorie::with('images')->latest()->first();
        $storyData = $story
            ? formatTranslatableData(
                $story,
                ['title'],
                ['id', 'url_video'],
                ['images' => 'images']
            )
            : null;

        //  Contact Info Section
        $contact = ContactInfo::latest()->first();
        $contactData = $contact
            ? formatTranslatableData(
                $contact,
                ['title', 'description'],
                ['id', 'youtube', 'whatsapp', 'facebook', 'instagram' , 'address' , 'phone' ,'email']
            )
            : null;

        //  التحقق إن الكل مش فاضي
        if (
            !$heroData && !$journeyData && !$serviceData &&
            !$tsetsData && !$coursesData && !$featureData &&
            !$storyData && !$contactData
        ) {
            return mainResponse(false, 'No homepage data found.', [], [], 404, null, false);
        }

        //  الاستجابة النهائية
        return mainResponse(true, 'Fetched homepage content successfully.', [
            'hero_section'      => $heroData,
            'journey_section'   => $journeyData,
            'service_section'   => $serviceData,
            'tsets'             => $tsetsData,
            'courses'           => $coursesData,
            'feature_section'   => $featureData,
            'success_story'     => $storyData,
            'contact_info'      => $contactData,
        ], [], 200, null, false);
    }












    // public function heroSection($locale)
    //     {
    //         app()->setLocale($locale);
    //         $heroSection = HeroSection::with('imageHero')
    //             ->where('status', 1)
    //             ->latest()
    //             ->first();
    //         if (!$heroSection) {
    //             return mainResponse(false, 'No active services found.', [], [], 404, null, false);
    //         }
    //         $data = formatTranslatableData(
    //             $heroSection,
    //             ['title', 'description', 'button_text'],
    //             ['id', 'button_link'],
    //             ['imageHero' => 'image']
    //         );
    //         return mainResponse(true, 'Fetched Hero sections successfully.', $data, [], 200, null, false);
    //     }


    // public  function journeySection($locale)
    // {
    //     app()->setLocale($locale);
    //     $heroSection = JourneySection::with(['images', 'features'])
    //         ->latest()
    //         ->first();
    //     if (!$heroSection) {
    //         return mainResponse(false, 'No active services found.', [], [], 404, null, false);
    //     }
    //     $data = formatTranslatableData(
    //         $heroSection,
    //         ['title', 'description'],
    //         ['id'],
    //         ['images' => 'images', 'features' => 'features']
    //     );
    //     return mainResponse(true, 'Fetched Hero sections successfully.', ['JourneySection' => $data], [], 200, null, false);
    // }

    //  public  function ServiceSection($locale)
    // {
    //     app()->setLocale($locale);
    //     $ServiceSection = ServiceSection::with(['image', 'features'])
    //         ->latest()
    //         ->first();
    //     if (!$ServiceSection) {
    //         return mainResponse(false, 'No active services found.', [], [], 404, null, false);
    //     }
    //     $data = formatTranslatableData(
    //         $ServiceSection,
    //         ['title'],
    //         ['id'],
    //         ['image' => 'image', 'features' => 'features']
    //     );
    //     return mainResponse(true, 'Fetched Hero sections successfully.', ['JourneySection' => $data], [], 200, null, false);
    // }
    //  public function tsets($locale)
    // {
    //     app()->setLocale($locale);

    //     $tests = Course::with('image')
    //         ->where('status', 1)
    //         ->latest()
    //         ->take(3)
    //         ->get();
    //     if ($tests->isEmpty()) {
    //         return mainResponse(false, 'No active services found.', [], [], 404, null, false);
    //     }
    //     $data = $tests->map(function ($test) {
    //         return formatTranslatableData(
    //             $test,
    //             ['title', 'description'],
    //             ['id'],
    //             ['image' => 'image']
    //         );
    //     });
    //     return mainResponse(true, 'Fetched Hero sections successfully.', $data, [], 200, null, false);
    // }
    //   public function courses($locale)
    // {
    //     app()->setLocale($locale);

    //     $tests = Course::with('image')
    //         ->where('status', 1)
    //         ->latest()
    //         ->take(3)
    //         ->get();
    //     if ($tests->isEmpty()) {
    //         return mainResponse(false, 'No active courses found.', [], [], 404, null, false);
    //     }
    //     $data = $tests->map(function ($test) {
    //         return formatTranslatableData(
    //             $test,
    //             ['title', 'description'],
    //             ['id'],
    //             ['image' => 'image']
    //         );
    //     });
    //     return mainResponse(true, 'Fetched courses sections successfully.', $data, [], 200, null, false);
    // }
    // public  function FeatureSection($locale)
    // {
    //     app()->setLocale($locale);
    //     $ServiceSection = FeatureSection::with(['image', 'features'])
    //         ->latest()
    //         ->first();
    //     if (!$ServiceSection) {
    //         return mainResponse(false, 'No active services found.', [], [], 404, null, false);
    //     }
    //     $data = formatTranslatableData(
    //         $ServiceSection,
    //         ['title'],
    //         ['id'],
    //         ['image' => 'image', 'features' => 'features']
    //     );
    //     return mainResponse(true, 'Fetched Hero sections successfully.', ['JourneySection' => $data], [], 200, null, false);
    // }
    //     public function SuccessStorie($locale)
    // {
    //     app()->setLocale($locale);

    //     $story = SuccessStorie::with('images')
    //         ->latest()
    //         ->first();

    //     if (!$story) {
    //         return mainResponse(false, 'No active courses found.', [], [], 404, null, false);
    //     }

    //     $data = formatTranslatableData(
    //         $story,
    //         ['title'],
    //         ['id', 'url_video'],
    //         ['images' => 'images']
    //     );

    //     return mainResponse(true, 'Fetched course section successfully.', ['SuccessStory' => $data], [], 200, null, false);
    // }

    // public function contact_infos($locale)
    // {
    //     app()->setLocale($locale);
    //     $contact_infos = ContactInfo::latest()
    //         ->first();
    //     if (!$contact_infos) {
    //         return mainResponse(false, 'No active services found.', [], [], 404, null, false);
    //     }
    //     $data = formatTranslatableData(
    //         $contact_infos,
    //         ['sub_title', 'sub_description',],
    //         ['id', 'youtube', 'whatsapp', 'facebook', 'instagram'],
    //     );
    //     return mainResponse(true, 'Fetched Hero sections successfully.', $data, [], 200, null, false);
    // }

}
