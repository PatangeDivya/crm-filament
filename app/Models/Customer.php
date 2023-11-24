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

    public function pipelineStages()
    {
        return $this->belongsToMany(
            PipelineStage::class,
            'customer_pipeline_stage',
            'customer_id',
            'pipeline_stage_id'
        )
            ->withPivot('pipeline_stage_id', 'customer_id', 'notes')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);   
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
