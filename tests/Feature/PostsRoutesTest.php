<?php

namespace Tests\Feature;

use App\Models\PostTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Post;
use App\Models\Language;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostsRoutesTest extends TestCase
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
     * Test for getting post list.
     */
    public function test_post_index_returns_posts_list()
    {
        $posts = $this->createPosts(5);

        $this->getJson(route('posts.index'), ['page' => 1, 'per_page' => 5])
            ->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'content',
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]);
    }

    /**
     * Test for find post by id.
     */
    public function test_post_can_be_found_by_id(): void
    {
        $post = $this->createPosts()->first();

        $this->getJson(route('posts.find-by-id', ['post' => $post->id, 'lang' => 'en']))
            ->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->where('data.title', $post->translations()->first()->title)
            );
    }

    /**
     * Test for adding post.
     */
    public function test_post_can_be_created()
    {
        $this->postJson(route('posts.store'), [
            'title' => $title = $this->faker->sentence,
            'description' => $this->faker->sentence,
            'content' => $this->faker->text,
            'lang' => 'ua',
        ])->assertStatus(201);

        $this->assertDatabaseHas('post_translations', ['title' => $title]);
    }

    public function test_post_can_be_updated()
    {
        $post = $this->createPosts()->first();
        $title = $post->translations()->first()->title;
        $lang = $post->translations()->first()->language->prefix;

        $this->putJson(route('posts.update', ['post' => $post->id, 'lang' => $lang]), [
            'title' => $newTitle = $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->text,
        ])->assertStatus(201);

        $this->assertNotEquals($newTitle, $title);
        $this->assertDatabaseHas('post_translations', ['title' => $newTitle]);
    }

    public function test_post_can_be_deleted()
    {
        $post = Post::create();
        $post->translations()->create([
            'title' => $title = $this->faker->sentence,
            'description' => $this->faker->sentence,
            'content' => $this->faker->text,
            'language_id' => Language::create(['prefix' => 'en'])->first()->id,
        ]);

        $this->assertDatabaseHas('post_translations', ['title' => $title]);

        $this->deleteJson(route('posts.destroy', ['post' => $post->id, 'lang' => 'en']))
            ->assertStatus(204);

        $this->assertDatabaseMissing('post_translations', ['post_id' => $post->id, 'title' => $title]);
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
