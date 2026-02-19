<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'amount',
        'status',
        'items',
        'papi_payment_url',
        'papi_notif_token',
        'shipping_address',
    ];

    protected $casts = [
        // items est stocké en JSON dans la DB
        // Laravel le convertit automatiquement en array PHP
        'items' => 'array',
    ];

    // ── Relations ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accesseurs utiles ────────────────────────────────────────

    /**
     * Montant formaté en Ariary pour affichage
     * Usage : $order->formatted_amount → "150 000 MGA"
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 0, ',', ' ') . ' MGA';
    }

    /**
     * Nombre total d'articles dans la commande
     */
    public function getTotalItemsAttribute(): int
    {
        return collect($this->items)->sum('quantity');
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}