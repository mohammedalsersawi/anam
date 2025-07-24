<?php

use App\Models\Upload;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;

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
