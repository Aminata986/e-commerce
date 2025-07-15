<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture Commande #{{ $order->id }}</title>
</head>
<body>
    <h1>Facture Commande #{{ $order->id }}</h1>
    <p>Client : {{ $order->user->name }}</p>
    <p>Date : {{ $order->created_at->format('d/m/Y') }}</p>
    <p>Adresse de livraison : {{ $order->shipping_address }}</p>
    <hr>
    <h3>Produits :</h3>
    <ul>
        @foreach($order->orderItems as $item)
            <li>
                {{ $item->product->name }} - {{ $item->quantity }} x {{ $item->price }} €
            </li>
        @endforeach
    </ul>
    <hr>
    <p>Total : {{ $order->total }} €</p>
    <p>Mode de paiement : {{ $order->payment_method }}</p>
</body>
</html>