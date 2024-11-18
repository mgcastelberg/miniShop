<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products, 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function seed(){
        // Ruta del archivo JSON
        $path = 'public/products.json';
        // Verifica si el archivo existe
        if (Storage::exists($path)) {
            // Lee el contenido del archivo
            $jsonContent = Storage::get($path);

            // Decodifica el JSON a un array
            $products = json_decode($jsonContent, true);

            // Verifica que el contenido sea un array
            if (is_array($products)) {

                DB::table('products')->truncate();
                // Inserta los productos en la tabla
                foreach ($products as $product) {
                    // dd($product);
                    DB::table('products')->insert([
                        'uuid' => $product['id'],
                        'title' => $product['title'],
                        'price' => $product['price'] ?? 0,
                        'description' => $product['description'] ?? null,
                        'slug' => $product['slug'] ?? null,
                        'stock' => $product['stock'] ?? 0,
                        'gender' => $product['gender'] ?? null,
                        'sizes' => isset($product['sizes']) ? json_encode(  explode(",", trim($product['sizes'], "{}"))  ) : null,
                        'tags' => isset($product['tags']) ? json_encode(  explode(",", trim($product['tags'], "{}"))  ) : null,
                        'user_id' => 4,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                return response()->json("SEED EJECUTED", 200);
            }

        } else {
            // Manejo de error si el archivo no existe
            throw new \Exception("El archivo $path no existe.");
        }
    }
}
