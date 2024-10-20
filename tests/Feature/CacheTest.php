<?php
use Tests\TestCase;
use Illuminate\Support\Facades\Cache; // To work with the cache
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class CacheTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getUser_is_cached_after_first_retrieval()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        // Ensure cache is empty initially
        Cache::shouldReceive('get')
             ->with('getUser' . $user->id)
             ->once()
             ->andReturn(null);

        // Retrieve user profile (this should cache the result)
        $response = $this->getJson("/api/getUser/{$user->id}");
        $response->assertStatus(200);

        // Now ensure the user profile is cached
        Cache::shouldReceive('put')
             ->with('getUser' . $user->id, $user->toArray(), 600)
             ->once();

        // Re-run the request, the cache should now contain the user profile
        $this->getJson("/api/getUser/{$user->id}");
    }

    /** @test */
    public function cached_user_profile_is_retrieved()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        // Simulate the profile being cached
        Cache::shouldReceive('get')
             ->with('getUser-' . $user->id)
             ->andReturn($user->toArray());

        // The response should now return the cached profile
        $response = $this->getJson("/api/getUser/{$user->id}");
        $response->assertStatus(200)
                 ->assertJson($user->toArray());
    }
}
