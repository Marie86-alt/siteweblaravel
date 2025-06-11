{{-- resources/views/emails/orders/confirmation.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmation de commande</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #78e6a6, #4ade80);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .order-items {
            margin: 20px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .item:last-child {
            border-bottom: none;
        }
        .item-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .item-info p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .item-price {
            font-weight: 600;
            color: #4ade80;
        }
        .total {
            background: #4ade80;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: #4ade80;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            background: #ffc107;
            color: #333;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Commande Confirmée !</h1>
            <p>Merci pour votre commande, {{ $user->name }}</p>
        </div>

        <div class="content">
            <h2>Détails de votre commande</h2>

            <div class="order-info">
                <p><strong>Numéro de commande :</strong> #{{ $order->id }}</p>
                <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                <p><strong>Statut :</strong> <span class="status-badge">{{ ucfirst($order->status) }}</span></p>
                @if($order->delivery_address)
                <p><strong>Adresse de livraison :</strong><br>
                   {{ $order->delivery_address }}<br>
                   {{ $order->delivery_postal_code }} {{ $order->delivery_city }}
                </p>
                @endif
            </div>

            <h3>Articles commandés</h3>
            <div class="order-items">
                @foreach($items as $item)
                <div class="item">
                    <div class="item-info">
                        <h4>{{ $item->product_name }}</h4>
                        <p>Quantité : {{ $item->quantity }} × {{ number_format($item->price, 2) }}€</p>
                    </div>
                    <div class="item-price">
                        {{ number_format($item->total, 2) }}€
                    </div>
                </div>
                @endforeach
            </div>

            <div class="total">
                Total : {{ number_format($order->total_amount, 2) }}€
            </div>

            <div style="text-align: center;">
                <a href="{{ route('customer.orders.show', $order) }}" class="btn">
                    Voir ma commande
                </a>
            </div>

            <h3>Que se passe-t-il maintenant ?</h3>
            <ul>
                <li>✅ Votre commande a été reçue et confirmée</li>
                <li>📦 Nous préparons vos articles avec soin</li>
                <li>🚚 Expédition sous 24-48h ouvrées</li>
                <li>📧 Vous recevrez un email de suivi</li>
            </ul>
        </div>

        <div class="footer">
            <p>Besoin d'aide ? Contactez-nous :</p>
            <p>📞 01 23 45 67 89 | 📧 support@fruits-legumes.fr</p>
            <p>Merci de faire confiance à Fruits & Légumes !</p>
        </div>
    </div>
</body>
</html>

{{-- resources/views/emails/orders/status-update.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mise à jour de commande</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #78e6a6, #4ade80);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .status-update {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            background: #0ea5e9;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0;
        }
        .timeline {
            margin: 30px 0;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        .timeline-icon.completed {
            background: #4ade80;
            color: white;
        }
        .timeline-icon.current {
            background: #0ea5e9;
            color: white;
        }
        .timeline-icon.pending {
            background: #e5e7eb;
            color: #6b7280;
        }
        .btn {
            display: inline-block;
            background: #4ade80;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📦 Mise à jour de commande</h1>
            <p>Bonjour {{ $user->name }}</p>
        </div>

        <div class="content">
            <div class="status-update">
                <h2>Votre commande #{{ $order->id }}</h2>
                <p>Le statut a été mis à jour :</p>
                <div class="status-badge">{{ ucfirst($statusMessage) }}</div>
            </div>

            <div class="timeline">
                <h3>Suivi de votre commande</h3>

                <div class="timeline-item">
                    <div class="timeline-icon completed">✓</div>
                    <div>
                        <strong>Commande passée</strong><br>
                        <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon {{ in_array($order->status, ['confirmed', 'preparing', 'shipped', 'delivered']) ? 'completed' : ($order->status == 'pending' ? 'current' : 'pending') }}">
                        {{ in_array($order->status, ['confirmed', 'preparing', 'shipped', 'delivered']) ? '✓' : '2' }}
                    </div>
                    <div>
                        <strong>Commande confirmée</strong><br>
                        <small>Commande acceptée et en traitement</small>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon {{ in_array($order->status, ['preparing', 'shipped', 'delivered']) ? 'completed' : ($order->status == 'confirmed' ? 'current' : 'pending') }}">
                        {{ in_array($order->status, ['preparing', 'shipped', 'delivered']) ? '✓' : '3' }}
                    </div>
                    <div>
                        <strong>Préparation</strong><br>
                        <small>Vos articles sont préparés avec soin</small>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : ($order->status == 'preparing' ? 'current' : 'pending') }}">
                        {{ in_array($order->status, ['shipped', 'delivered']) ? '✓' : '4' }}
                    </div>
                    <div>
                        <strong>Expédition</strong><br>
                        <small>Votre commande est en route</small>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon {{ $order->status == 'delivered' ? 'completed' : ($order->status == 'shipped' ? 'current' : 'pending') }}">
                        {{ $order->status == 'delivered' ? '✓' : '5' }}
                    </div>
                    <div>
                        <strong>Livraison</strong><br>
                        <small>Commande livrée à votre adresse</small>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('customer.orders.show', $order) }}" class="btn">
                    Voir ma commande
                </a>
            </div>

            @if($order->status == 'shipped')
            <div style="background: #fef3c7; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h4 style="color: #92400e; margin: 0 0 10px 0;">🚚 Votre commande est en route !</h4>
                <p style="color: #92400e; margin: 0;">Vous devriez la recevoir sous 24-48h. Restez disponible pour la réception.</p>
            </div>
            @endif

            @if($order->status == 'delivered')
            <div style="background: #d1fae5; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h4 style="color: #065f46; margin: 0 0 10px 0;">🎉 Commande livrée !</h4>
                <p style="color: #065f46; margin: 0;">Nous espérons que vous êtes satisfait(e) de vos achats. N'hésitez pas à nous laisser un avis !</p>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Questions ? Contactez-nous :</p>
            <p>📞 01 23 45 67 89 | 📧 support@fruits-legumes.fr</p>
            <p>Merci de faire confiance à Fruits & Légumes !</p>
        </div>
    </div>
</body>
</html>
