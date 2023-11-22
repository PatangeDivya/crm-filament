<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);   
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);   
    }

    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class);   
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);   
    }
}
