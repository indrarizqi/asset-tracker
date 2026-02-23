<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Mengambil semua ID aset tanpa pagination
     */
    public function test_get_all_asset_ids_returns_all_ids(): void
    {
        // Arrange: Buat user dan login
        $user = User::factory()->create();
        
        // Buat beberapa aset untuk testing
        $assets = Asset::factory()->count(25)->create();
        
        // Act: Panggil endpoint
        $response = $this->actingAs($user)->get(route('assets.all-ids'));
        
        // Assert: Verifikasi response
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'total',
            'ids'
        ]);
        
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals(25, $data['total']);
        $this->assertCount(25, $data['ids']);
        
        // Verifikasi bahwa semua ID aset ada dalam response (tanpa mempedulikan urutan)
        $assetIds = $assets->pluck('id')->toArray();
        sort($assetIds);
        sort($data['ids']);
        $this->assertEquals($assetIds, $data['ids']);
    }

    /**
     * Test: Mengambil ID aset dengan filter search (by name)
     */
    public function test_get_all_asset_ids_with_search_filter_by_name(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        // Buat aset dengan nama yang berbeda
        Asset::factory()->create(['name' => 'Laptop Dell']);
        Asset::factory()->create(['name' => 'Laptop HP']);
        Asset::factory()->create(['name' => 'Mouse Logitech']);
        Asset::factory()->create(['name' => 'Keyboard Mechanical']);
        
        // Act: Search dengan keyword "laptop"
        $response = $this->actingAs($user)->get(route('assets.all-ids', ['search' => 'laptop']));
        
        // Assert
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertEquals(2, $data['total']); // Hanya 2 laptop
        $this->assertCount(2, $data['ids']);
    }

    /**
     * Test: Mengambil ID aset dengan filter search (by asset_tag)
     */
    public function test_get_all_asset_ids_with_search_filter_by_asset_tag(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        Asset::factory()->create(['asset_tag' => 'M-26-001']);
        Asset::factory()->create(['asset_tag' => 'M-26-002']);
        Asset::factory()->create(['asset_tag' => 'F-26-001']);
        
        // Act: Search dengan keyword "M-26"
        $response = $this->actingAs($user)->get(route('assets.all-ids', ['search' => 'M-26']));
        
        // Assert
        $data = $response->json();
        $this->assertEquals(2, $data['total']);
    }

    /**
     * Test: Mengambil ID aset dengan filter search (by category)
     */
    public function test_get_all_asset_ids_with_search_filter_by_category(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        Asset::factory()->create(['category' => 'mobile']);
        Asset::factory()->create(['category' => 'mobile']);
        Asset::factory()->create(['category' => 'fixed']);
        Asset::factory()->create(['category' => 'semi-mobile']);
        
        // Act: Search dengan keyword "mobile" (akan match mobile dan semi-mobile)
        $response = $this->actingAs($user)->get(route('assets.all-ids', ['search' => 'mobile']));
        
        // Assert
        $data = $response->json();
        $this->assertEquals(3, $data['total']); // 2 mobile + 1 semi-mobile
    }

    /**
     * Test: Endpoint memerlukan autentikasi
     */
    public function test_get_all_asset_ids_requires_authentication(): void
    {
        // Act: Akses tanpa login
        $response = $this->get(route('assets.all-ids'));
        
        // Assert: Redirect ke login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test: Response kosong jika tidak ada data
     */
    public function test_get_all_asset_ids_returns_empty_when_no_assets(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        // Act: Panggil endpoint tanpa ada data aset
        $response = $this->actingAs($user)->get(route('assets.all-ids'));
        
        // Assert
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertTrue($data['success']);
        $this->assertEquals(0, $data['total']);
        $this->assertEmpty($data['ids']);
    }

    /**
     * Test: Search dengan keyword yang tidak match
     */
    public function test_get_all_asset_ids_returns_empty_when_search_not_found(): void
    {
        // Arrange
        $user = User::factory()->create();
        Asset::factory()->count(5)->create(['name' => 'Laptop']);
        
        // Act: Search dengan keyword yang tidak ada
        $response = $this->actingAs($user)->get(route('assets.all-ids', ['search' => 'xyz123notfound']));
        
        // Assert
        $data = $response->json();
        $this->assertEquals(0, $data['total']);
        $this->assertEmpty($data['ids']);
    }

    /**
     * Test: Response dalam format JSON yang benar
     */
    public function test_get_all_asset_ids_returns_correct_json_format(): void
    {
        // Arrange
        $user = User::factory()->create();
        $asset = Asset::factory()->create();
        
        // Act
        $response = $this->actingAs($user)->get(route('assets.all-ids'));
        
        // Assert: Verifikasi format response
        $response->assertJson([
            'success' => true,
            'total' => 1,
            'ids' => [$asset->id]
        ]);
    }
}
