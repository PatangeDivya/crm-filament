<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_default'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
