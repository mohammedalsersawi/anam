<?php

namespace App\Http\Controllers\Api\Admin\AboutBlock;

use App\Models\Upload;
use App\Models\AboutItem;
use App\Models\AboutBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAboutBlockRequest;

class AboutBlockController extends Controller
{

    public function store(StoreAboutBlockRequest $request)
    {
        try {
            $data = localizedRequestData(
                $request,
                ['title', 'description'],
                ['type', 'updated_by']
            );
            $adminId = auth('admin')->id();
            $data['updated_by'] = $adminId;
            $block = AboutBlock::updateOrCreate(
                ['type' => $request->type],
                $data + ['created_by' => $adminId]
            );
            if (!in_array($request->type, ['hero', 'goals']) && $request->has('items')) {
                $block->items()->delete();
                foreach ($request->items as $itemText) {
                    AboutItem::create([
                        'about_block_id' => $block->id,
                        'text' => $itemText,
                    ]);
                }
            }
            UploadImage($request->image, AboutBlock::PATH_IMAGE, AboutBlock::class, $block->id, true, null, Upload::IMAGE);
            return mainResponse(true, 'Block saved successfully.', compact('block'), [], 200, null, false);
        } catch (\Exception $e) {
            return mainResponse(false, 'Something went wrong.', [], ['server' => [$e->getMessage()]], 500, null, false);
        }
    }
}
