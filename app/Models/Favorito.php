<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';
    
    protected $fillable = [
        'user_id',
        'favorable_id',
        'favorable_type',
        'fecha_marcado'
    ];

    /**
     * Obtener el usuario al que pertenece este favorito
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el elemento marcado como favorito (polimórfico)
     */
    public function favorable(): MorphTo
    {
        return $this->morphTo();
    }
}