<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Voir le panier
    public function show(Request $request)
    {
        $cart = $this->getCart($request);
        return response()->json($cart->load('items.product'));
    }

    // Ajouter un article
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $cart = $this->getCart($request);
        $product = Product::findOrFail($request->product_id);
        $item = $cart->items()->where('product_id', $product->id)->first();
        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }
        $this->updateTotal($cart);
        return response()->json($cart->load('items.product'));
    }

    // Modifier la quantité d'un article
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $item = CartItem::findOrFail($itemId);
        $item->quantity = $request->quantity;
        $item->save();
        $this->updateTotal($item->cart);
        return response()->json($item->cart->load('items.product'));
    }

    // Supprimer un article
    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $cart = $item->cart;
        $item->delete();
        $this->updateTotal($cart);
        return response()->json($cart->load('items.product'));
    }

    // Méthode utilitaire pour récupérer le panier (user ou session)
    private function getCart(Request $request)
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = $request->session()->getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }
        return $cart;
    }

    // Met à jour le total du panier
    private function updateTotal(Cart $cart)
    {
        $total = $cart->items()->sum(
            \DB::raw('quantity * price')
        );
        $cart->total = $total;
        $cart->save();
    }
} 