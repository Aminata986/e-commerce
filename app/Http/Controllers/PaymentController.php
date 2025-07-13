<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

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
}