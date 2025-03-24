<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
    public function usuariosCompartidos()
{
    return $this->belongsToMany(User::class, 'usuarios_tableros', 'tablero_id', 'user_id')
                ->withPivot('fecha_compartido')
                ->withTimestamps();
}
public function isOwnedBy($userId)
{
    return $this->id_creador == $userId;
}
public function favoritos(): MorphMany
{
    return $this->morphMany(Favorito::class, 'favorable');
}
public function esFavoritoDe($userId)
{
    return $this->favoritos()->where('user_id', $userId)->exists();
} 
}