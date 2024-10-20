<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test retrieving user profile with Redis caching.
     */
    public function testGetUserProfileWithRedisCaching()
    {
        // Create a dummy user in the database
        $user = User::factory()->create();

        // Mock Redis to simulate caching behavior
        Redis::shouldReceive('get')
            ->once()
            ->with("user:{$user->id}")
            ->andReturn(null); // No cached version initially

        Redis::shouldReceive('setex')
            ->once()
            ->with("user:{$user->id}", 86400, json_encode($user));

        // Make a GET request to retrieve the user
        $response = $this->getJson("/api/user/{$user->id}");

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
    }

    /**
     * Test retrieving user profile when data is found in Redis cache.
     */
    public function testGetUserProfileFromRedisCache()
    {
        // Create a dummy user in the database
        $user = User::factory()->create();

        // Mock Redis to return cached user data
        Redis::shouldReceive('get')
            ->once()
            ->with("user:{$user->id}")
            ->andReturn(json_encode($user));

        // No need to set the cache since the data is already cached
        Redis::shouldReceive('setex')->never();

        // Make a GET request to retrieve the user
        $response = $this->getJson("/api/user/{$user->id}");

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);
    }

    /**
     * Test updating user profile and refreshing Redis cache.
     */
    public function testUpdateUserProfileAndRefreshRedisCache()
    {
        // Create a dummy user in the database
        $user = User::factory()->create();

        // New data to update the user profile
        $updatedData = [
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'phone' => '9876543210',
        ];

        // Mock Redis for cache invalidation
        Redis::shouldReceive('setex')
            ->once()
            ->with("user:{$user->id}", 86400, json_encode(array_merge($user->toArray(), $updatedData)));

        // Make a PUT request to update the user
        $response = $this->putJson("/api/user/{$user->id}", $updatedData);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $updatedData['name'],
                'email' => $updatedData['email'],
                'phone' => $updatedData['phone'],
            ]);

        // Assert that the database was updated correctly
        $this->assertDatabaseHas('users', $updatedData);
    }

    /**
     * Test retrieving a non-existent user.
     */
    public function testGetNonExistentUser()
    {
        // Try to retrieve a non-existent user (ID 9999)
        $response = $this->getJson("/api/user/9999");

        // Assert that the response is 404
        $response->assertStatus(404)
            ->assertJson([
                'error' => 'User not found',
            ]);
    }
}
