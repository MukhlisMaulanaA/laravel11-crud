<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
  use HasFactory;

  /**
   * fillable
   *
   * @var array
   */
  protected $fillable = [
    'image',
    'title',
    'description',
    'price',
    'stock',
  ];

  public function getImageUrl($imageName)
  {
    return Storage::disk('public')->url('products/' . $imageName);
  }
}