<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function user_can_login_and_get_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function author_can_create_article()
    {
        $author = User::factory()->create([
            'role' => 'Author',
        ]);

        Sanctum::actingAs($author, ['*']);
        $payload = [
            'title'   => 'My Test Article',
            'content' => 'This is the article body.',
        ];

        $response = $this->postJson('/api/articles', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'My Test Article',
            ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'My Test Article',
        ]);
    }

    public function viewer_cannot_create_article()
    {
        $viewer = User::factory()->create([
            'role' => 'Viewer',
        ]);

        Sanctum::actingAs($viewer, ['*']);

        $payload = [
            'title'   => 'Unauthorized Article',
            'content' => 'Should not be created.',
        ];

        $response = $this->postJson('/api/articles', $payload);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are not authorized to perform this action.',
            ]);
    }

    
}
