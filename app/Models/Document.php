<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $guarded = [];

    protected $casts = [
        'doc_date' => 'date',
        'subtotal' => 'decimal:2',
        'payment' => 'decimal:2',
    ];

    public const TYPES = [
        'quotation' => 'Quotation',
        'invoice' => 'Invoice',
        'delivery_order' => 'Delivery Order',
    ];

    public const PREFIXES = [
        'quotation' => 'Q0',
        'invoice' => 'Inv',
        'delivery_order' => 'DO',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(DocumentItem::class)->orderBy('position');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->subtotal - (float) $this->payment;
    }

    public function isQuotation(): bool
    {
        return $this->type === 'quotation';
    }

    public function isInvoice(): bool
    {
        return $this->type === 'invoice';
    }

    public function isDeliveryOrder(): bool
    {
        return $this->type === 'delivery_order';
    }

    /**
     * Recalculate the subtotal from the related items.
     */
    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('amount');
        $this->save();
    }
}
