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
        return $this->belongsToMany(
            Customer::class,
            'customer_pipeline_stage',
            'pipeline_stage_id',
            'customer_id'
        )
            ->withPivot('customer_id')
            ->withTimestamps();
    }
}
