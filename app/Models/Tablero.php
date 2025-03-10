<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tablero extends Model
{
    use HasFactory;
    protected $table = 'tableros'; 
    protected $primaryKey = 'id_tablero'; 

    // Permitir autoincremento
    public $incrementing = true; 

    protected $keyType = 'int';

    protected $fillable = ['nombre', 'id_creador', 'color_fondo'];
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'id_creador');
    }
    
    public function tareas()
    {
        return $this->hasMany(Kanban::class, 'id_tablero');
    }
}