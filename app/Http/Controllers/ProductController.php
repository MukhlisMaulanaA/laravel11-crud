<?php

namespace App\Http\Controllers;

//import model product
use App\Models\Product;

//import return type View
use Illuminate\View\View;

//import return type redirectResponse
use Illuminate\Http\Request;

//import Http Request
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
  /**
   * index
   *
   * @return void
   */
  public function index(): View
  {
    //get all products
    $products = Product::latest()->paginate(10);

    //render view with products
    return view('products.index', compact('products'));
  }

  /**
   * create
   *
   * @return View
   */
  public function create(): View
  {
    return view('products.create');
  }

  /**
   * store
   *
   * @param  mixed $request
   * @return RedirectResponse
   */
  public function store(Request $request)
  {
    // Validasi form
    $validated = $request->validate([
      'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
      'title' => 'required|min:5',
      'description' => 'required|min:10',
      'price' => 'required|numeric',
      'stock' => 'required|numeric'
    ]);

    try {
      // Upload dan simpan gambar
      if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = $image->hashName(); // Generate nama unik

        // Simpan gambar ke storage publik
        Storage::disk('public')->put('products/' . $imageName, file_get_contents($image));

        // Buat produk dengan data yang divalidasi
        Product::create([
          'image' => $imageName, // Simpan hanya nama file
          'title' => $validated['title'],
          'description' => $validated['description'],
          'price' => $validated['price'],
          'stock' => $validated['stock']
        ]);

        return redirect()
          ->route('products.index')
          ->with('success', 'Data Berhasil Disimpan!');
      }
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
    }
  }

  public function show(string $id): View
  {
    //get product by ID
    $product = Product::findOrFail($id);

    //render view with product
    return view('products.show', compact('product'));
  }

  public function edit(string $id): View
  {
    //get product by ID
    $product = Product::findOrFail($id);

    //render view with product
    return view('products.edit', compact('product'));
  }

  public function update(Request $request, Product $product)
  {
    // Validasi form
    $rules = [
      'title' => 'required|min:5',
      'description' => 'required|min:10',
      'price' => 'required|numeric',
      'stock' => 'required|numeric'
    ];

    // Tambahkan validasi gambar jika ada upload gambar baru
    if ($request->hasFile('image')) {
      $rules['image'] = 'required|image|mimes:jpeg,jpg,png|max:2048';
    }

    $validated = $request->validate($rules);

    try {
      // Handle image upload jika ada gambar baru
      if ($request->hasFile('image')) {
        // Hapus gambar lama
        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
          Storage::disk('public')->delete('products/' . $product->image);
        }

        // Upload gambar baru
        $image = $request->file('image');
        $imageName = $image->hashName();
        Storage::disk('public')->put('products/' . $imageName, file_get_contents($image));

        // Update data produk dengan gambar baru
        $product->update([
          'image' => $imageName,
          'title' => $validated['title'],
          'description' => $validated['description'],
          'price' => $validated['price'],
          'stock' => $validated['stock']
        ]);
      } else {
        // Update data produk tanpa mengubah gambar
        $product->update([
          'title' => $validated['title'],
          'description' => $validated['description'],
          'price' => $validated['price'],
          'stock' => $validated['stock']
        ]);
      }

      return redirect()
        ->route('products.index')
        ->with('success', 'Data Berhasil Diperbarui!');

    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
    }
  }

  public function destroy($id): RedirectResponse
  {
    //get product by ID
    $product = Product::findOrFail($id);

    //delete image
    Storage::delete('public/products/' . $product->image);

    //delete product
    $product->delete();

    //redirect to index
    return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus!']);
  }

}