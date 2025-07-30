<?php

use App\Models\Upload;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\BlogArticle;
use App\Models\Keyword;

function mainResponse($status, $msg, $items, $validator, $code = 200, $pages = null, $showPages = true)
{
    $item_with_paginate = $items;
    if (gettype($items) == 'array') {
        if (count($items)) {
            $item_with_paginate = $items[array_key_first($items)];
        }
    }

    if ($showPages && isset(json_decode(json_encode($item_with_paginate, true), true)['data'])) {
        $pagination = json_decode(json_encode($item_with_paginate, true), true);
        $new_items = $pagination['data'];
        $pages = [
            "current_page" => $pagination['current_page'],
            "first_page_url" => $pagination['first_page_url'],
            "from" => $pagination['from'],
            "last_page" => $pagination['last_page'],
            "last_page_url" => $pagination['last_page_url'],
            "next_page_url" => $pagination['next_page_url'],
            "path" => $pagination['path'],
            "per_page" => $pagination['per_page'],
            "prev_page_url" => $pagination['prev_page_url'],
            "to" => $pagination['to'],
            "total" => $pagination['total'],
        ];
    } elseif ($showPages) {
        $pages = [
            "current_page" => 0,
            "first_page_url" => '',
            "from" => 0,
            "last_page" => 0,
            "last_page_url" => '',
            "next_page_url" => null,
            "path" => '',
            "per_page" => 0,
            "prev_page_url" => null,
            "to" => 0,
            "total" => 0,
        ];
    }

    if (gettype($items) == 'array') {
        if (count($items)) {
            $new_items = [];
            foreach ($items as $key => $item) {
                if (isset(json_decode(json_encode($item, true), true)['data'])) {
                    $pagination = json_decode(json_encode($item, true), true);
                    $new_items[$key] = $pagination['data'];
                } else {
                    $new_items[$key] = $item;
                }
                $items = $new_items;
            }
        }
    } else {
        if (isset(json_decode(json_encode($item_with_paginate, true), true)['data'])) {
            $pagination = json_decode(json_encode($item_with_paginate, true), true);
            $items = $pagination['data'];
        }
    }

    $aryErrors = [];
    foreach ($validator as $key => $value) {
        $aryErrors[] = ['field_name' => $key, 'messages' => $value];
    }

    $newData = [
        'status' => $status,
        'message' => __($msg),
        'data' => $items,
        'errors' => $aryErrors
    ];

    if ($showPages) {
        $newData['pages'] = $pages;
    }

    return response()->json($newData, $code);
}




function locales()
{
    $arr = [];
    foreach (LaravelLocalization::getSupportedLocales() as $key => $value) {
        $arr[$key] = __('' . $value['name']);
    }
    return $arr;
}
function UploadImage($file, $path = null, $model, $relation_id, $update = false, $id = null, $type, $name = null, $deleteOldImages = false)
{
    try {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $newName = 'p' . rand() . time() . '.' . $extension;

        $cleanPath = ltrim($path, '/');
        Storage::disk('public')->putFileAs($cleanPath, $file, $newName);

        $fullOriginalPath = asset('storage/' . $cleanPath . $newName);
        $relativePath = $cleanPath . $newName;

        $data = [
            'name' => $name ?? $originalName,
            'filename' => $newName,
            'full_original_path' => $fullOriginalPath,
            'path' => $relativePath,
            'relation_id' => $relation_id,
            'relation_type' => $model,
            'type' => $type,
            'extension' => $extension,
        ];

        // حذف الصور القديمة إذا طُلب ذلك
        if ($deleteOldImages) {
            $oldImages = Upload::where('relation_id', $relation_id)
                ->where('relation_type', $model)
                ->where('type', $type)
                ->get();

            foreach ($oldImages as $img) {
                if (Storage::disk('public')->exists($img->path)) {
                    Storage::disk('public')->delete($img->path);
                }
                $img->delete();
            }
        }

        if (!$update) {
            return Upload::create($data);
        } else {
            $query = Upload::query()
                ->where('relation_id', $relation_id)
                ->where('relation_type', $model);

            if ($name) {
                $query->where('name', $name);
            } else {
                $query->where('type', $type);
            }

            $image = $id ? Upload::find($id) : $query->first();

            if ($image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->update($data);
                return $relativePath;
            } else {
                return Upload::create($data);
            }
        }
    } catch (\Exception $e) {
        return false;
    }
}


function generateLocalizedSlugs(array $names, $separator = '-'): array
{
    $slugs = [];

    foreach ($names as $locale => $value) {
        // تنظيف وتوليد أولي
        $slug = trim($value);
        $slug = mb_strtolower($slug, 'UTF-8');
        $slug = preg_replace('/[^\p{Arabic}a-zA-Z0-9\s\-]+/u', '', $slug); // أبقي الحروف اللاتينية
        $slug = preg_replace('/[\s\-]+/u', $separator, $slug);
        $slug = trim($slug, $separator);

        $originalSlug = $slug;
        $counter = 1;

        // التحقق من التكرار في قاعدة البيانات
        while (
            BlogArticle::where("slug->{$locale}", $slug)->exists()
        ) {
            $slug = $originalSlug . $separator . $counter;
            $counter++;
        }

        $slugs[$locale] = $slug;
    }

    return $slugs;
}


function UploadImageOld($file, $path = null, $model, $relation_id, $update = false, $id = null, $type, $name = null)
{
    try {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $newName = 'p' . rand() . time() . '.' . $extension;

        // إزالة أي / من بداية المسار
        $cleanPath = ltrim($path, '/');

        // حفظ الصورة في storage/app/public
        Storage::disk('public')->putFileAs($cleanPath, $file, $newName);

        // مسار العرض للواجهة (public/storage/...)
        $fullOriginalPath = asset('storage/' . $cleanPath . $newName);
        // المسار النسبي للتخزين
        $relativePath = $cleanPath . $newName;

        $data = [
            'name' => $name ?? $originalName,
            'filename' => $newName,
            'full_original_path' => $fullOriginalPath,
            'path' => $relativePath, // بدون "/" في البداية
            'relation_id' => $relation_id,
            'relation_type' => $model,
            'type' => $type,
            'extension' => $extension,
        ];

        if (!$update) {
            return Upload::create($data);
        } else {
            $query = Upload::query()
                ->where('relation_id', $relation_id)
                ->where('relation_type', $model);

            if ($name) {
                $query->where('name', $name);
            } else {
                $query->where('type', $type);
            }

            $image = $id ? Upload::find($id) : $query->first();

            if ($image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->update($data);
                return $relativePath;
            } else {
                return Upload::create($data);
            }
        }
    } catch (\Exception $e) {
        return false;
    }
}



if (!function_exists('toggleModelBooleanField')) {
    function toggleModelBooleanField(string $modelClass, $id, string $field = 'status')
    {
        try {
            /** @var Model|null $model */
            $model = $modelClass::find($id);

            if (!$model) {
                return [
                    'success' => false,
                    'message' => 'Item not found.',
                    'errors' => ['id' => ['Item with ID ' . $id . ' not found.']],
                    'status' => 404
                ];
            }

            $model->update([
                $field => !$model->$field
            ]);

            return [
                'success' => true,
                'message' => ucfirst($field) . ' toggled successfully.',
                'data' => [
                    'id' => $model->id,
                    'new_status' => $model->fresh()->$field
                ],
                'status' => 200
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while toggling ' . $field . '.',
                'errors' => ['server' => [$e->getMessage()]],
                'status' => 500
            ];
        }
    }
}





if (!function_exists('localizedRequestData')) {
    /**
     * Generate structured multilingual and static request data for storing in JSON fields.
     *
     * @param Request $request
     * @param array $localizedFields Fields that should be treated as multilingual (e.g., title, description)
     * @param array $normalFields Fields to take directly from the request (e.g., price, status)
     * @return array
     */
    function localizedRequestData(Request $request, array $localizedFields, array $normalFields): array
    {
        $data = [];

        foreach ($localizedFields as $field) {
            foreach (locales() as $key => $language) {
                $data[$field][$key] = $request->get("{$field}_{$key}");
            }
        }

        return collect($data)->merge($request->only($normalFields))->all();
    }
}


function formatLocalizedDate($datetime, $locale = 'ar')
{
    Carbon::setLocale($locale);

    $months = [
        'en' => [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ],
        'ar' => [
            1 => 'كانون الثاني',
            2 => 'شباط',
            3 => 'آذار',
            4 => 'نيسان',
            5 => 'أيار',
            6 => 'حزيران',
            7 => 'تموز',
            8 => 'آب',
            9 => 'أيلول',
            10 => 'تشرين الأول',
            11 => 'تشرين الثاني',
            12 => 'كانون الأول'
        ],
    ];

    $carbonDate = Carbon::parse($datetime);
    $monthNum = (int)$carbonDate->format('n');
    $day = $carbonDate->format('j');
    $year = $carbonDate->format('Y');

    $monthName = $months[$locale][$monthNum] ?? $carbonDate->format('F');

    return "$day $monthName $year";
}



/**
 * تجهيز البيانات المترجمة + الحقول الثابتة + العلاقات الجانبية.
 *
 * @param Illuminate\Database\Eloquent\Model|Arrayable $model
 * @param array $translatableFields أسماء الحقول المترجمة مثل ['title', 'description']
 * @param array $extraFields أسماء الحقول العادية التي تريد إضافتها كما هي
 * @param array $relationsMap علاقات بصيغة ['image' => 'image', 'features' => 'features']
 * @return array
 */

function formatTranslatableData($model, array $translatableFields, array $extraFields = [], array $relationsMap = []): array
{
    $locale = app()->getLocale();
    $data = [];

    // ✅ الحقول المترجمة
    foreach ($translatableFields as $field) {
        if (method_exists($model, 'getTranslation')) {
            $data[$field] = $model->getTranslation($field, $locale);
        } else {
            $data[$field] = $model->$field ?? null;
        }
    }

    // ✅ الحقول العادية + تنسيق التاريخ
    foreach ($extraFields as $field) {
        if (in_array($field, ['created_at', 'updated_at']) && $model->$field) {
            $data[$field] = formatLocalizedDate($model->$field, $locale);
        } else {
            $data[$field] = $model->$field ?? null;
        }
    }

    // ✅ العلاقات
    foreach ($relationsMap as $relationKey => $outputField) {
        // دعم: relation أو relation.field
        $parts = explode('.', $relationKey);
        $relationName = $parts[0];
        $specificField = $parts[1] ?? null;

        $relation = $model->$relationName ?? null;

        // ✅ علاقة hasMany
        if ($relation instanceof \Illuminate\Support\Collection) {
            $data[$outputField] = $relation->map(function ($item) use ($locale) {
                if (method_exists($item, 'formatForApi')) {
                    return $item->formatForApi();
                } elseif (isset($item->path)) {
                    return asset('storage/' . $item->path);
                }

                $result = [];

                if (property_exists($item, 'translatable') && is_array($item->translatable)) {
                    foreach ($item->translatable as $field) {
                        if (method_exists($item, 'getTranslation')) {
                            $result[$field] = $item->getTranslation($field, $locale);
                        }
                    }
                }

                foreach ($item->getAttributes() as $key => $value) {
                    if (!array_key_exists($key, $result) && $key !== 'pivot') {
                        $result[$key] = $value;
                    }
                }

                return $result;
            })->toArray();

            // ✅ علاقة belongsTo
        } elseif ($relation instanceof \Illuminate\Database\Eloquent\Model) {
            // حقل معين فقط من العلاقة
            if ($specificField) {
                if (method_exists($relation, 'getTranslation')) {
                    $data[$outputField] = $relation->getTranslation($specificField, $locale);
                } else {
                    $data[$outputField] = $relation->$specificField ?? null;
                }
            }
            // كائن كامل باستخدام formatForApi
            elseif (method_exists($relation, 'formatForApi')) {
                $data[$outputField] = $relation->formatForApi();
            }
            // صورة داخل العلاقة
            elseif (isset($relation->path)) {
                $data[$outputField] = asset('storage/' . $relation->path);
            }
            // الكائن كما هو
            else {
                $data[$outputField] = $relation;
            }
        } else {
            $data[$outputField] = null;
        }
    }

    return $data;
}




/**
 * إضافة كلمة مفتاحية مترجمة إلى جدول keywords.
 *
 * @param array $translatedNames  مثال: ['ar' => 'برمجة', 'en' => 'Programming']
 * @param int $sectionId          رقم العنصر المرتبط (ID)
 * @param string $sectionType     نوع الكيان المرتبط (مثل App\Models\BlogArticle::class)
 * @param int $adminId            رقم الأدمن الذي أضاف الكلمة
 * @return \App\Models\Keyword|null
 */
function add_keyword(array $translatedNames, int $sectionId, string $sectionType, int $adminId): ?Keyword
{
    $existing = Keyword::where('section_id', $sectionId)
        ->where('section_type', $sectionType)
        ->get()
        ->first(function ($keyword) use ($translatedNames) {
            foreach ($translatedNames as $locale => $value) {
                if ($keyword->getTranslation('name', $locale, false) !== $value) {
                    return false;
                }
            }
            return true;
        });

    if ($existing) {
        return $existing;
    }
    $keyword = new Keyword();
    $keyword->setTranslations('name', $translatedNames);
    $keyword->section_id = $sectionId;
    $keyword->section_type = $sectionType;
    $keyword->created_by = $adminId;
    $keyword->save();
    return $keyword;
}




/**
 * مزامنة الكلمات المفتاحية: حذف القديمة وإضافة الجديدة.
 *
 * @param array $keywordsArray  مثال: [['ar' => 'برمجة', 'en' => 'Programming'], ['ar' => 'ويب', 'en' => 'Web']]
 * @param int $sectionId
 * @param string $sectionType
 * @param int $adminId
 * @return void
 */
function sync_keywords(array $keywordsArray, int $sectionId, string $sectionType, int $adminId): void
{
    // حذف الكلمات المفتاحية القديمة المرتبطة بنفس العنصر
    Keyword::where('section_id', $sectionId)
        ->where('section_type', $sectionType)
        ->delete();

    // إضافة الكلمات الجديدة
    foreach ($keywordsArray as $keywordTranslation) {
        add_keyword($keywordTranslation, $sectionId, $sectionType, $adminId);
    }
}

