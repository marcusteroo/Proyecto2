<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Workflow extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_workflow';
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'status', 
        'id_creador',
        'trigger_type',
        'trigger_params'
    ];

    /**
     * Los usuarios que tienen acceso a este workflow
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'usuarios_workflows', 'workflow_id', 'user_id')
                    ->withPivot('rol', 'fecha_compartido')
                    ->withTimestamps();
    }

    /**
     * El propietario del workflow
     */
    public function owner()
    {
        return $this->users()->wherePivot('rol', 'propietario')->first();
    }
    
    /**
     * Determina si un usuario es propietario del workflow
     */
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

    public function actions()
    {
        return $this->hasMany(WorkflowAction::class, 'id_workflow', 'id_workflow');
    }

    /**
     * Usuarios con rol propietario
     */
    public function propietarios()
    {
        return $this->users()->wherePivot('rol', 'propietario');
    }

    /**
     * Usuarios con roles diferentes a propietario
     */
    public function usuariosCompartidos()
    {
        return $this->users()->wherePivot('rol', '!=', 'propietario');
    }

    /**
     * Método para compartir un workflow con otro usuario
     * 
     * @param int $userId ID del usuario que comparte
     * @param int|array $userIds ID o array de IDs de usuarios
     * @param string $rol Rol a asignar (propietario, editor, espectador)
     * @return void
     */
    public function compartirCon($userId, $userIds, $rol = 'espectador')
    {
        if (!$this->isOwnedBy($userId)) {
            throw new \Exception('No tienes permisos para compartir este workflow');
        }
        
        if (!is_array($userIds)) {
            $userIds = [$userIds => ['rol' => $rol]];
        } else {
            $usersWithRoles = [];
            foreach ($userIds as $userId) {
                $usersWithRoles[$userId] = ['rol' => $rol];
            }
            $userIds = $usersWithRoles;
        }
        
        $this->users()->attach($userIds);
    }

    /**
     * Obtener tableros relacionados con este workflow
     */
    public function tableros()
    {
        return $this->hasMany(Tablero::class, 'workflow_id', 'id_workflow');
    }
}