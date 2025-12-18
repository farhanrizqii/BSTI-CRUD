<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index(Request $request)
{
    $query = Product::query();

    if ($request->filled('q')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->q . '%')
              ->orWhere('description', 'like', '%' . $request->q . '%');
        });
    }

    return view('products.index', [
        'products' => $query->latest()->get(),
        'q' => $request->q
    ]);
}
    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        try {
            // 1. Validasi input
            $request->validate([
                'name'  => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string',
            ]);

            // 2. Ambil file
            $file = $request->file('image');
            $fileName = uniqid() . '_' . $file->getClientOriginalName();

            // 3. Upload ke Supabase Storage
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                'apikey'        => env('SUPABASE_KEY'),
                'Content-Type'  => $file->getMimeType(),
            ])->send(
                'POST',
                env('SUPABASE_URL') . '/storage/v1/object/product-bsti/' . $fileName,
                [
                    'body' => fopen($file->getRealPath(), 'r'),
                ]
            );

            // Cek kalau upload gagal
            if ($response->failed()) {
                return back()
                    ->withInput()
                    ->with('error', 'Upload gambar gagal: ' . $response->body());
            }

            // 4. URL publik gambar
            $imageUrl = env('SUPABASE_URL')
                . '/storage/v1/object/public/product-bsti/'
                . $fileName;

            // 5. Simpan ke database
            Product::create([
                'name'        => $request->name,
                'description' => $request->description,
                'image'       => $imageUrl,
            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            // 1. Validasi input
            $request->validate([
                'name'  => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string',
            ]);

            $imageUrl = $product->image;

            // 2. Jika ada gambar baru
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = uniqid() . '_' . $file->getClientOriginalName();

                // 3. Upload ke Supabase Storage
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                    'apikey'        => env('SUPABASE_KEY'),
                    'Content-Type'  => $file->getMimeType(),
                ])->send(
                    'POST',
                    env('SUPABASE_URL') . '/storage/v1/object/product-bsti/' . $fileName,
                    [
                        'body' => fopen($file->getRealPath(), 'r'),
                    ]
                );

                if ($response->failed()) {
                    return back()
                        ->withInput()
                        ->with('error', 'Upload gambar gagal: ' . $response->body());
                }

                // 4. URL publik gambar baru
                $imageUrl = env('SUPABASE_URL')
                    . '/storage/v1/object/public/product-bsti/'
                    . $fileName;

                // 5. Hapus gambar lama dari Supabase (opsional)
                // Ekstrak nama file dari URL lama
                if ($product->image) {
                    $oldFileName = basename(parse_url($product->image, PHP_URL_PATH));
                    Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                        'apikey'        => env('SUPABASE_KEY'),
                    ])->delete(
                        env('SUPABASE_URL') . '/storage/v1/object/product-bsti/' . $oldFileName
                    );
                }
            }

            // 6. Update database
            $product->update([
                'name'        => $request->name,
                'description' => $request->description,
                'image'       => $imageUrl,
            ]);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product berhasil diupdate!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
{
    return view('products.show', compact('product'));
}

    public function destroy(Product $product)
    {
        try {
            // 1. Hapus gambar dari Supabase
            if ($product->image) {
                $fileName = basename(parse_url($product->image, PHP_URL_PATH));
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                    'apikey'        => env('SUPABASE_KEY'),
                ])->delete(
                    env('SUPABASE_URL') . '/storage/v1/object/product-bsti/' . $fileName
                );
            }

            // 2. Hapus dari database
            $product->delete();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product berhasil dihapus!');
                
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}