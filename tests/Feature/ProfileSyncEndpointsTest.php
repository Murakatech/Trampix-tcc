<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSyncEndpointsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_updates_returns_expected_structure_for_authenticated_user()
    {
        $user = User::factory()->create([
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/profile/check-updates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'changed',
                'data' => [
                    'photo_url',
                    'display_name',
                    'initials',
                    'role',
                    'email',
                    'has_photo',
                ],
                'timestamp',
            ])
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function check_updates_returns_304_not_modified_when_client_is_up_to_date()
    {
        $user = User::factory()->create([
            'updated_at' => now()->subMinute(),
        ]);

        $lastModified = $user->updated_at->format('D, d M Y H:i:s \G\M\T');

        $response = $this->actingAs($user)
            ->withHeaders(['If-Modified-Since' => $lastModified])
            ->getJson('/api/profile/check-updates');

        $response->assertStatus(304);
    }

    /** @test */
    public function profile_data_returns_expected_structure_for_authenticated_user()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/profile/data');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'display_name',
                    'email',
                    'role',
                    'photo_url',
                    'has_photo',
                    'initials',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'display_name' => 'John Doe',
                    'email' => 'john@example.com',
                    'initials' => 'JD',
                ],
            ]);
    }

    /** @test */
    public function malformed_if_modified_since_header_is_ignored()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['If-Modified-Since' => 'invalid-date'])
            ->getJson('/api/profile/check-updates');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function correct_cache_headers_are_returned()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/profile/check-updates');

        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'must-revalidate, no-cache, private')
            ->assertHeader('Last-Modified');
    }
}
