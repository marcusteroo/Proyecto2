<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'ratings';

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'user_id',
        'score',
        'comment',
        'categories',
        'job_position',
        'company'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Convertir las categorías de string a array al obtener el modelo
    public function getCategoriesAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }
    
    // Convertir las categorías de array a string al guardar
    public function setCategoriesAttribute($value)
    {
        $this->attributes['categories'] = is_array($value) ? implode(',', $value) : $value;
    }
}