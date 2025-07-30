<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('blog_categories')->insert([
            [
                'name' => json_encode([
                    'en' => 'Tip',
                    'ar' => 'نصيحة',
                ]),
                'slug' => json_encode([
                    'en' => 'tip',
                    'ar' => 'نصيحة',
                ]),
                'description' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Stories',
                    'ar' => 'قصص',
                ]),
                'slug' => json_encode([
                    'en' => 'stories',
                    'ar' => 'قصص',
                ]),
                'description' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Inspiring thoughts',
                    'ar' => 'أفكار ملهمة',
                ]),
                'slug' => json_encode([
                    'en' => 'inspiring-thoughts',
                    'ar' => 'أفكار-ملهمة',
                ]),
                'description' => null,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
