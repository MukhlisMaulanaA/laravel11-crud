<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('public');
  }

  /** @test */
  public function can_create_product()
  {
    $productData = [
      'title' => 'Test Product',
      'description' => 'This is a test product description',
      'price' => 9999,
      'stock' => 10,
      'image' => UploadedFile::fake()->image('test.jpg')
    ];

    $response = $this->post(route('products.store'), $productData);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
      'title' => 'Test Product',
      'description' => 'This is a test product description',
      'price' => 9999,
      'stock' => 10
    ]);

    $product = Product::first();
    Storage::disk('public')->assertExists('products/' . $product->image);
  }

  /** @test */
  public function can_update_product()
  {
    $product = Product::factory()->create();

    $updatedData = [
      'title' => 'Updated Product',
      'description' => 'This is an updated description',
      'price' => 8888,
      'stock' => 5,
      'image' => UploadedFile::fake()->image('new.jpg')
    ];

    $response = $this->put(route('products.update', $product), $updatedData);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
      'id' => $product->id,
      'title' => 'Updated Product',
      'description' => 'This is an updated description',
      'price' => 8888,
      'stock' => 5
    ]);
  }

  /** @test */
  public function can_delete_product()
  {
    $product = Product::factory()->create();

    $response = $this->delete(route('products.destroy', $product));

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing('products/' . $product->image);
  }

  /** @test */
  public function title_is_required()
  {
    $response = $this->post(route('products.store'), [
      'description' => 'Test description',
      'price' => 9999,
      'stock' => 10,
      'image' => UploadedFile::fake()->image('test.jpg')
    ]);

    $response->assertSessionHasErrors('title');
  }
}