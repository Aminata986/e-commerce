<?php

namespace App\Http\Controllers;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

// ... dans la méthode store()
Mail::to($order->user->email)->send(new OrderConfirmation($order));
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Validation stricte des statuts
        if (!in_array($validated['status'], Order::getStatusList())) {
            return response()->json(['error' => 'Statut de commande invalide'], 422);
        }
        if (!in_array($validated['payment_status'], Order::getPaymentStatusList())) {
            return response()->json(['error' => 'Statut de paiement invalide'], 422);
        }
        // Empêcher de livrer si non payé
        if ($validated['status'] === Order::STATUS_DELIVERED && $order->payment_status !== Order::PAYMENT_PAID) {
            return response()->json(['error' => 'Impossible de marquer comme livrée si la commande n\'est pas payée'], 422);
        }
        // Empêcher d'expédier si non payé
        if ($validated['status'] === Order::STATUS_SHIPPED && $order->payment_status !== Order::PAYMENT_PAID) {
            return response()->json(['error' => 'Impossible d\'expédier si la commande n\'est pas payée'], 422);
        }
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

    // Télécharger la facture PDF
    public function downloadInvoice($id)
    {
        $order = Order::with(['user', 'orderItems.product', 'payment'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        return $pdf->download('facture_commande_'.$order->id.'.pdf');
    }
}