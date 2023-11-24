<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'subtotal', 'taxes', 'total'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'quote_product', 'quote_id', 'product_id')
            ->withPivot('product_id', 'quote_id', 'price', 'quantity');
    }
}
