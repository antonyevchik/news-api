<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagsRoutesTest extends TestCase
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
    public function test_tags_index_returns_tags_list()
    {
        $tagsCount = 7;
        $this->createTags($tagsCount);

        $this->json('GET', route('tags.index'), ['page' => 1, 'per_page' => $tagsCount])
            ->assertStatus(200)
            ->assertJsonCount($tagsCount, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'posts',
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

    // /**
    //  * Test for adding post.
    //  */
    // public function test_post_can_be_created()
    // {
    //     $this->postJson(route('posts.store'), [
    //         'title' => $title = $this->faker->sentence,
    //         'description' => $this->faker->sentence,
    //         'content' => $this->faker->text,
    //         'lang' => 'ua',
    //         'tags' => ['tag1', 'tag2', 'tag3'],
    //     ])->assertStatus(201);

    //     $this->assertDatabaseHas('post_translations', ['title' => $title]);
    //     $this->assertDatabaseHas('tags', ['name' => 'tag3']);
    //     $this->assertDatabaseCount('tags', 3);
    //     $this->assertDatabaseCount('post_tags', 3);
    // }

    // /**
    //  * Test for updating post.
    //  */
    // public function test_post_can_be_updated()
    // {
    //     $post = $this->createPosts()->first();
    //     $title = $post->translations()->first()->title;
    //     $lang = $post->translations()->first()->language->prefix;

    //     $this->putJson(route('posts.update', ['post' => $post->id]), [
    //         'title' => $newTitle = $this->faker->sentence,
    //         'description' => $this->faker->paragraph,
    //         'content' => $this->faker->text,
    //         'lang' => $lang,
    //     ])->assertStatus(201);

    //     $this->assertNotEquals($newTitle, $title);
    //     $this->assertDatabaseHas('post_translations', ['title' => $newTitle]);
    // }

    // /**
    //  * Test for deleting post.
    //  */
    // public function test_post_can_be_deleted()
    // {
    //     $post = $this->createPosts()->first();
    //     $title = $post->translations()->first()->title;
    //     $lang = $post->translations()->first()->language->prefix;

    //     $this->assertDatabaseHas('post_translations', ['title' => $title]);

    //     $this->deleteJson(route('posts.destroy', ['post' => $post->id]), ['lang' => $lang])
    //         ->assertStatus(204);

    //     $this->assertDatabaseMissing('post_translations', ['post_id' => $post->id, 'title' => $title]);
    // }

    public function createTags(int $tagsCount = 1, int $postsCount = 1, $lang = 'en')
    {
        return Tag::factory()
            ->has(
                Post::factory()->count($postsCount)
            )
            ->count($tagsCount)
            ->create();
    }
}
