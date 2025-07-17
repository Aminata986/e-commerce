<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'status', 'payment_status', 'payment_method', 'shipping_address', 'total'
    ];

    // Statuts de commande
    public const STATUS_PENDING = 'en attente';
    public const STATUS_SHIPPED = 'expédiée';
    public const STATUS_DELIVERED = 'livrée';
    public const STATUS_CANCELLED = 'annulée';

    // Statuts de paiement
    public const PAYMENT_PAID = 'payé';
    public const PAYMENT_UNPAID = 'non payé';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function getPaymentStatusList()
    {
        return [
            self::PAYMENT_PAID,
            self::PAYMENT_UNPAID,
        ];
    }

    // Empêcher des transitions incohérentes
    public function canBeDelivered()
    {
        return $this->payment_status === self::PAYMENT_PAID && $this->status === self::STATUS_SHIPPED;
    }

    public function canBeShipped()
    {
        return $this->status === self::STATUS_PENDING && $this->payment_status === self::PAYMENT_PAID;
    }
}