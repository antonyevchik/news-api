<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostsRoutesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

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
        $postsCount = 7;
        $this->createPosts($postsCount);

        $this->json('GET', route('posts.index'), ['page' => 1, 'per_page' => $postsCount])
            ->assertStatus(200)
            ->assertJsonCount($postsCount, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'content',
                        'tags',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ]
            ]);
    }

    /**
     * Test for find post by id.
     */
    public function test_post_can_be_found_by_id(): void
    {
        $post = $this->createPosts()->first();

        $this->json('GET', route('posts.find-by-id', ['post' => $post->id]), ['lang' => 'en'])
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('data.title', $post->translations()->first()->title)
            );
    }

    /**
     * Test for adding post.
     */
    public function test_post_can_be_created()
    {
        $this->postJson(route('posts.store'), [
            'title'       => $title = $this->faker->sentence,
            'description' => $this->faker->sentence,
            'content'     => $this->faker->text,
            'lang'        => 'ua',
            'tags'        => ['tag1', 'tag2', 'tag3'],
        ])->assertStatus(201);

        $this->assertDatabaseHas('post_translations', ['title' => $title]);
        $this->assertDatabaseHas('tags', ['name' => 'tag3']);
        $this->assertDatabaseCount('tags', 3);
        $this->assertDatabaseCount('post_tags', 3);
    }

    /**
     * Test to add translation
     */
    public function test_it_is_possible_to_add_translation()
    {
        $post = $this->createPosts(1, 'ua')->first();

        $this->assertDatabaseCount('post_translations', 1)
            ->assertDatabaseHas('post_translations', ['post_id' => $post->id]);

        $this->postJson(route('posts.store'), [
            'post_id'     => $post->id,
            'title'       => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content'     => $this->faker->text,
            'lang'        => 'en',
        ])->assertStatus(201);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('post_translations', 2)
            ->assertDatabaseHas('post_translations', ['post_id' => $post->id]);
    }

    /**
     * Test for updating post.
     */
    public function test_post_can_be_updated()
    {
        $post  = $this->createPosts()->first();
        $title = $post->translations()->first()->title;
        $lang  = $post->translations()->first()->language->prefix;

        $this->putJson(route('posts.update', ['post' => $post->id]), [
            'title'       => $newTitle = $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content'     => $this->faker->text,
            'lang'        => $lang,
        ])->assertStatus(201);

        $this->assertNotEquals($newTitle, $title);
        $this->assertDatabaseHas('post_translations', ['title' => $newTitle]);
    }

    /**
     * Test for deleting post.
     */
    public function test_post_can_be_deleted()
    {
        $post  = $this->createPosts()->first();
        $title = $post->translations()->first()->title;
        $lang  = $post->translations()->first()->language->prefix;

        $this->assertDatabaseHas('post_translations', ['title' => $title]);

        $this->deleteJson(route('posts.destroy', ['post' => $post->id]), ['lang' => $lang])
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
