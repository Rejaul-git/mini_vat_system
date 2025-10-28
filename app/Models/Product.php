<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sku', 'unit', 'vat_rate'];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Calculate available stock
    public function getAvailableStockAttribute()
    {
        $purchased = $this->purchaseItems()->sum('qty');
        $sold = $this->saleItems()->sum('qty');
        $returned = ReturnItem::whereHas('saleItem', function($query) {
            $query->where('product_id', $this->id);
        })->sum('qty');

        return $purchased - $sold + $returned;
    }

    // Get total purchased quantity
    public function getTotalPurchasedAttribute()
    {
        return $this->purchaseItems()->sum('qty');
    }

    // Get total sold quantity
    public function getTotalSoldAttribute()
    {
        return $this->saleItems()->sum('qty');
    }

    // Get total returned quantity
    public function getTotalReturnedAttribute()
    {
        return ReturnItem::whereHas('saleItem', function($query) {
            $query->where('product_id', $this->id);
        })->sum('qty');
    }
}