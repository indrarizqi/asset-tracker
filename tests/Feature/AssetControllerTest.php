<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_asset_ids_returns_all_ids_for_authenticated_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $assets = Asset::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('assets.all-ids'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'total' => 3,
            ]);

        $ids = $response->json('ids');

        $this->assertCount(3, $ids);
        $this->assertEqualsCanonicalizing($assets->pluck('id')->all(), $ids);
    }

    public function test_get_all_asset_ids_requires_authentication(): void
    {
        $response = $this->getJson(route('assets.all-ids'));

        $response->assertUnauthorized();
    }
}
