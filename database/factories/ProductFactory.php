<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
  protected $model = Product::class;

  public function definition()
  {
    return [
      'title' => $this->faker->sentence(3),
      'description' => $this->faker->paragraph,
      'price' => $this->faker->numberBetween(1000, 100000),
      'stock' => $this->faker->numberBetween(1, 100),
      'image' => $this->faker->image('public/storage/products', 400, 300, null, false)
    ];
  }
}