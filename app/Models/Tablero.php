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
    
    /**
     * El usuario creador del tablero
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'id_creador');
    }
    
    /**
     * Las tareas asociadas al tablero
     */
    public function tareas()
    {
        return $this->hasMany(Kanban::class, 'id_tablero');
    }
    
    /**
     * Usuarios con los que se ha compartido este tablero
     */
    public function usuariosCompartidos()
    {
        return $this->belongsToMany(User::class, 'usuarios_tableros', 'tablero_id', 'user_id')
                    ->withPivot('fecha_compartido')
                    ->withTimestamps();
    }
    
    /**
     * Determina si un usuario es propietario del tablero
     */
    public function isOwnedBy($userId)
    {
        return $this->id_creador == $userId;
    }
    
    /**
     * Relación con favoritos
     */
    public function favoritos(): MorphMany
    {
        return $this->morphMany(Favorito::class, 'favorable');
    }
    
    /**
     * Verifica si es favorito de un usuario
     */
    public function esFavoritoDe($userId)
    {
        return $this->favoritos()->where('user_id', $userId)->exists();
    }
    
    /**
     * Compartir este tablero con usuarios especificados
     *
     * @param int|array $userIds Los IDs de usuarios o array de IDs
     * @return void
     */
    public function compartirCon($userIds)
    {
        $this->usuariosCompartidos()->attach($userIds);
    }
    
    /**
     * Actualiza la lista de usuarios que tienen acceso a este tablero
     *
     * @param array $userIds Array con IDs de usuarios
     * @param bool $detach Si true, elimina usuarios no incluidos en el array
     * @return array
     */
    public function actualizarUsuarios($userIds, $detach = false)
    {
        if ($detach) {
            return $this->usuariosCompartidos()->sync($userIds);
        } else {
            return $this->usuariosCompartidos()->syncWithoutDetaching($userIds);
        }
    }
    
    /**
     * Crea un nuevo tablero y lo asigna al usuario creador
     *
     * @param array $data Datos del tablero
     * @param int $userId ID del usuario creador
     * @return Tablero
     */
    public static function crearConPropietario(array $data, $userId)
    {
        $data['id_creador'] = $userId;
        $tablero = self::create($data);
        
        // Compartir con el creador
        $tablero->usuariosCompartidos()->attach($userId);
        
        return $tablero;
    }
}