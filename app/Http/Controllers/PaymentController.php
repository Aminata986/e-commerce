<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    // Liste des paiements
    public function index()
    {
        return response()->json(Payment::with('order')->get());
    }

    // Créer un paiement
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
        ]);
        $payment = Payment::create($validated);
        return response()->json($payment, 201);
    }

    // Afficher un paiement
    public function show($id)
    {
        $payment = Payment::with('order')->findOrFail($id);
        return response()->json($payment);
    }

    // Mettre à jour un paiement
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
        ]);
        $payment->update($validated);
        return response()->json($payment);
    }

    // Supprimer un paiement
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(['message' => 'Paiement supprimé avec succès']);
    }

    // Simuler un paiement en ligne
    public function simulateOnlinePayment(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|in:card,paypal',
        ]);
        $order = Order::findOrFail($orderId);
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json(['error' => 'Cette commande est déjà payée'], 422);
        }
        // Simuler le succès du paiement
        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'payment_method' => $request->payment_method,
        ]);
        $payment = $order->payment()->create([
            'amount' => $order->total,
            'method' => $request->payment_method,
            'status' => 'completed',
        ]);
        return response()->json([
            'message' => 'Paiement simulé avec succès',
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    // Marquer comme payé à la livraison (admin)
    public function markAsPaidOnDelivery($orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json(['error' => 'Cette commande est déjà payée'], 422);
        }
        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'payment_method' => 'livraison',
        ]);
        $payment = $order->payment()->create([
            'amount' => $order->total,
            'method' => 'livraison',
            'status' => 'completed',
        ]);
        return response()->json([
            'message' => 'Paiement à la livraison enregistré',
            'order' => $order,
            'payment' => $payment,
        ]);
    }
}