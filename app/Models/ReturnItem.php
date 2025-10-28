<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = ['sale_item_id', 'qty', 'date', 'reason'];

    protected $casts = [
        'date' => 'date',
    ];

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }
}