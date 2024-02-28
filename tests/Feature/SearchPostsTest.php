<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchPostsTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->make();
        $this->actingAs($this->user);
    }

    /**
     * Test the posts can be searched.
     */
    public function test_posts_can_be_searched(): void
    {
        $this->createPosts(10);

        $this->getJson(route('posts.search'))
            ->assertStatus(200);
    }

    public function createPosts(int $count = 1, $lang = 'en')
    {
        return Post::factory()
            ->has(
                PostTranslation::factory()
                    ->has(Language::factory(['prefix' => $lang])),
                'translations'
            )
            ->count($count)
            ->create();
    }
}
