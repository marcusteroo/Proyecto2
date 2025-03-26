<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtarea extends Model
{
    use HasFactory;

    protected $table = 'subtareas'; // Nombre de la tabla en la DB
    protected $primaryKey = 'id_subtarea'; 
    public $timestamps = true; // Habilita created_at y updated_at

    // Usamos "estado" en lugar de "completado"
    protected $fillable = [
        'titulo',
        'estado',
        'id_tarea'
    ];

    // Cast de estado para asegurarnos de que se trate como entero
    protected $casts = [
        'estado' => 'integer',
    ];

    // Ocultar la relación para evitar problemas de serialización
    protected $hidden = ['tarea'];

    public function tarea()
    {
        return $this->belongsTo(Kanban::class, 'id_tarea');
    }
}
