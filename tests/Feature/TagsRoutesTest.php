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

    /**
     * Test for adding tag.
     */
    public function test_tag_can_be_created()
    {
        $this->postJson(route('tags.store'), [
            'name' => $name = $this->faker->word,
        ])->assertStatus(201);

        $this->assertDatabaseHas('tags', ['name' => $name]);
    }

    /**
     * Test for updating tag.
     */
    public function test_tag_can_be_updated()
    {
        $tag = $this->createtags()->first();
        $name = $tag->name;

        $this->putJson(route('tags.update', ['tag' => $tag->id]), [
            'name' => $newName = $this->faker->word,
        ])->assertStatus(201);

        $this->assertNotEquals($newName, $name);
        $this->assertDatabaseHas('tags', ['name' => $newName]);
    }

    /**
     * Test for deleting tag.
     */
    public function test_post_can_be_deleted()
    {
        $tag = $this->createTags()->first();
        $name = $tag->name;

        $this->assertDatabaseHas('tags', ['name' => $name]);

        $this->deleteJson(route('tags.destroy', ['tag' => $tag->id]))
            ->assertStatus(204);

        $this->assertDatabaseMissing('tags', ['id' => $tag->id, 'name' => $name]);
    }

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
