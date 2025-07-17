<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 40%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        .customer-info {
            margin-bottom: 30px;
        }
        .customer-info h3 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE</h1>
        <h2>Votre Boutique E-commerce</h2>
    </div>

    <div class="company-info">
        <strong>Votre Boutique E-commerce</strong><br>
        123 Rue du Commerce<br>
        75001 Paris, France<br>
        Tél: +33 1 23 45 67 89<br>
        Email: contact@votreboutique.com
    </div>

    <div class="invoice-info">
        <strong>Facture #{{ $order->id }}</strong><br>
        Date: {{ $order->created_at->format('d/m/Y') }}<br>
        Statut: {{ ucfirst($order->status) }}<br>
        Paiement: {{ ucfirst($order->payment_status) }}
    </div>

    <div class="clear"></div>

    <div class="customer-info">
        <h3>Informations Client</h3>
        <strong>{{ $order->user->name }}</strong><br>
        Email: {{ $order->user->email }}<br>
        Adresse de livraison:<br>
        {{ $order->shipping_address }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ number_format($item->price, 2) }} €</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price * $item->quantity, 2) }} €</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                <td><strong>{{ number_format($order->total, 2) }} €</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Merci pour votre commande !</p>
        <p>Cette facture a été générée automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>