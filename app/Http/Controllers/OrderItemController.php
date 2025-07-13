<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    // Liste des items de commande
    public function index()
    {
        return response()->json(OrderItem::with(['order', 'product'])->get());
    }

    // Créer un item de commande
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);
        $orderItem = OrderItem::create($validated);
        return response()->json($orderItem, 201);
    }

    // Afficher un item de commande
    public function show($id)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($id);
        return response()->json($orderItem);
    }

    // Mettre à jour un item de commande
    public function update(Request $request, $id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $validated = $request->validate([
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);
        $orderItem->update($validated);
        return response()->json($orderItem);
    }

    // Supprimer un item de commande
    public function destroy($id)
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();
        return response()->json(['message' => 'Item supprimé avec succès']);
    }
}