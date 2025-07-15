<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Liste des produits
    public function index()
    {
        return response()->json(Product::with('category')->get());
    }

    // Créer un produit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    // Afficher un produit
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    // Mettre à jour un produit
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        $product->update($validated);
        return response()->json($product);
    }

    // Supprimer un produit
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }
}