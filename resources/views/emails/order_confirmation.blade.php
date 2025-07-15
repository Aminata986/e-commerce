<h1>Merci pour votre commande #{{ $order->id }}</h1>
<p>Bonjour {{ $order->user->name }},</p>
<p>Votre commande a bien été enregistrée.</p>
<p>Total : {{ $order->total }} €</p>