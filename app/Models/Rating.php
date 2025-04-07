<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'comment',
        'categories',
        'job_position',
        'company',
        'featured',
        'featured_order',
        'photo_path',
        'tags',
        'verified'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'verified' => 'boolean',
        'tags' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('featured', true)
                    ->orderBy('featured_order')
                    ->limit(6);
    }
}