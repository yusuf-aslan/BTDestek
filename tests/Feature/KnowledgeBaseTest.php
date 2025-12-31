<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KnowledgeBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_table_has_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('articles'));
        $this->assertTrue(Schema::hasColumns('articles', [
            'id',
            'category_id',
            'title',
            'slug',
            'content',
            'is_published',
            'published_at',
            'views',
            'created_at',
            'updated_at',
        ]));
    }

    public function test_article_model_can_be_created(): void
    {
        $category = Category::create(['name' => 'Software']);

        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'How to reset your password',
            'slug' => 'how-to-reset-your-password',
            'content' => 'Follow these steps...',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'How to reset your password',
            'slug' => 'how-to-reset-your-password',
        ]);
    }

    public function test_article_belongs_to_category(): void
    {
        $category = Category::create(['name' => 'Software']);
        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'Content',
        ]);

        $this->assertInstanceOf(Category::class, $article->category);
        $this->assertEquals($category->id, $article->category->id);
    }

    public function test_category_has_many_articles(): void
    {
        $category = Category::create(['name' => 'Software']);
        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'Content',
        ]);

        $this->assertTrue($category->articles->contains($article));
    }
}
