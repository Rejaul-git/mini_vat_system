<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_name', 'note'];

    protected $casts = [
        'date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Calculate subtotal
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->qty * $item->unit_price;
        });
    }

    // Calculate VAT
    public function getVatAmountAttribute()
    {
        return $this->items->sum(function($item) {
            return ($item->qty * $item->unit_price * $item->vat_rate) / 100;
        });
    }

    // Calculate grand total
    public function getGrandTotalAttribute()
    {
        return $this->subtotal + $this->vat_amount;
    }
}