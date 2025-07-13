<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Liste des commandes
    public function index()
    {
        return response()->json(Order::with(['user', 'orderItems', 'payment'])->get());
    }

    // Créer une commande
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
            'payment_status' => 'required|string',
            'payment_method' => 'required|string',
            'shipping_address' => 'required|string',
            'total' => 'required|numeric',
        ]);
        $order = Order::create($validated);
        return response()->json($order, 201);
    }

    // Afficher une commande
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems', 'payment'])->findOrFail($id);
        return response()->json($order);
    }

    // Mettre à jour une commande
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|string',
            'payment_status' => 'required|string',
            'payment_method' => 'required|string',
            'shipping_address' => 'required|string',
            'total' => 'required|numeric',
        ]);
        $order->update($validated);
        return response()->json($order);
    }

    // Supprimer une commande
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Commande supprimée avec succès']);
    }
}