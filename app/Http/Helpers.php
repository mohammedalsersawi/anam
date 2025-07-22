<?php

use App\Models\Upload;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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


function UploadImage($file, $path = null, $model, $relation_id, $update = false, $id = null, $type, $name = null)
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
