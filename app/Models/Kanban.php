<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kanban extends Model
{
    use HasFactory;

    protected $table = 'tareas'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_tarea'; // Clave primaria personalizada
    public $timestamps = true; // Habilita created_at y updated_at

    protected $fillable = ['titulo', 'descripcion', 'estado', 'id_tablero'];

    // Relación con el usuario (suponiendo que id_tablero referencia a la tabla users)
    public function tablero()
    {
        return $this->belongsTo(User::class, 'id_tablero');
    }
        // En el modelo Kanban.php
    public function subtareas()
    {
        return $this->hasMany(Subtarea::class, 'id_tarea');
}
}