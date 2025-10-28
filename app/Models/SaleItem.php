<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'product_id', 'qty', 'unit_price', 'vat_rate'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnItem::class);
    }

    // Get total returned quantity for this item
    public function getReturnedQtyAttribute()
    {
        return $this->returns()->sum('qty');
    }

    // Get net quantity (sold - returned)
    public function getNetQtyAttribute()
    {
        return $this->qty - $this->returned_qty;
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