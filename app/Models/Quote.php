<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'subtotal', 'taxes', 'total'];

    public function quoteProducts()
    {
        return $this->hasMany(QuoteProduct::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
