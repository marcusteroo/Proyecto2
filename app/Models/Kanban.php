<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kanban extends Model
{
    use HasFactory;

    protected $table = 'tableros'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_tablero'; // Clave primaria personalizada
    public $timestamps = true; // Habilita created_at y updated_at

    protected $fillable = ['nombre', 'descripcion', 'id_creador'];

    // Relación con el usuario (suponiendo que id_creador referencia a la tabla users)
    public function creador()
    {
        return $this->belongsTo(User::class, 'id_creador');
    }
}