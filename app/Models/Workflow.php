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
}