<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ArticleCategory::factory(10)->create();
        $articleCategory = [
            [
                'uuid' => '06578a54-cb07-4c5f-a143-e18199c5788f',
                'article_category_name' => 'Dukungan',
                'article_category_slug' => 'dukungan',
                'created_at' => '2025-06-02 12:00:05',
                'updated_at' => '2025-06-02 12:00:05'
            ],
            [
                'uuid' => '40c1752a-e1ac-4d5a-ae40-2718e58ebe81',
                'article_category_name' => 'Karya Ilmiah',
                'article_category_slug' => 'karya-ilmiah',
                'created_at' => '2025-05-23 14:16:07',
                'updated_at' => '2025-05-23 14:16:07',
            ],
            [
                'uuid' => '51f67a85-183c-4b23-af03-9a0662c563ad',
                'article_category_name' => 'Berita UMKM',
                'article_category_slug' => 'berita-umkm',
                'created_at' => '2025-05-23 13:44:03',
                'updated_at' => '2025-06-02 13:30:24',
            ],
            [
                'uuid' => '5f709af3-5dbb-42e6-bc89-12a611c6ea7b',
                'article_category_name' => 'Koperasi',
                'article_category_slug' => 'koperasi',
                'created_at' => '2025-06-02 12:44:16',
                'updated_at' => '2025-06-02 12:44:16',
            ],
        ];

        foreach ($articleCategory as $data) {
            $model = new ArticleCategory($data);
            $model->timestamps = false;
            $model->save();
        }
    }
}
