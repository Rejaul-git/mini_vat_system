<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'product_id', 'qty', 'unit_price', 'vat_rate'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate line total
    public function getLineTotalAttribute()
    {
        return $this->qty * $this->unit_price;
    }

    // Calculate line VAT
    public function getLineVatAttribute()
    {
        return ($this->line_total * $this->vat_rate) / 100;
    }

    // Calculate line grand total
    public function getLineGrandTotalAttribute()
    {
        return $this->line_total + $this->line_vat;
    }
}