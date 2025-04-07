<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'password',
        'surname1',
        'surname2'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    public function assignaments()
    {
        return $this->hasMany(UserAssignment::class,'user_id');
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
             ->singleFile()
             ->useFallbackUrl('/images/placeholder.jpg');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->nonQueued();
    }   
    
    /**
     * Todos los workflows a los que el usuario tiene acceso
     */
    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'usuarios_workflows', 'user_id', 'workflow_id')
                ->withPivot('rol', 'fecha_compartido')
                ->withTimestamps();
    }
    
    /**
     * Workflows que el usuario posee (rol propietario)
     */
    public function ownedWorkflows()
    {
        return $this->workflows()->wherePivot('rol', 'propietario');
    }
    
    /**
     * Workflows compartidos con el usuario (no es propietario)
     */
    public function sharedWorkflows()
    {
        return $this->workflows()->wherePivot('rol', '!=', 'propietario');
    }
    
    /**
     * Tableros compartidos con este usuario
     */
    public function tablerosCompartidos()
    {
        return $this->belongsToMany(Tablero::class, 'usuarios_tableros', 'user_id', 'tablero_id')
                ->withPivot('fecha_compartido')
                ->withTimestamps();
    }
    
    /**
     * Todos los favoritos del usuario
     */
    public function favoritos()
    {
        return $this->hasMany(Favorito::class);
    }
    
    /**
     * Workflows marcados como favoritos
     */
    public function workflowsFavoritos()
    {
        return $this->belongsToMany(Workflow::class, 'favoritos', 'user_id', 'favorable_id')
            ->where('favorable_type', Workflow::class)
            ->withTimestamps();
    }
    
    /**
     * Tableros marcados como favoritos
     */
    public function tablerosFavoritos()
    {
        return $this->belongsToMany(Tablero::class, 'favoritos', 'user_id', 'favorable_id')
            ->where('favorable_type', Tablero::class)
            ->withTimestamps();
    }
    
    /**
     * Método para compartir un workflow con otro usuario
     * 
     * @param int $workflowId ID del workflow a compartir
     * @param int|array $userIds ID o array de IDs de usuarios
     * @param string $rol Rol a asignar (propietario, editor, espectador)
     * @return void
     */
    public function compartirWorkflow($workflowId, $userIds, $rol = 'espectador')
    {
        $workflow = Workflow::findOrFail($workflowId);
        
        if (!$workflow->isOwnedBy($this->id)) {
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
        
        $workflow->users()->attach($userIds);
    }
    
    /**
     * Método para compartir un tablero con otro usuario
     * 
     * @param int $tableroId ID del tablero a compartir
     * @param int|array $userIds ID o array de IDs de usuarios
     * @return void
     */
    public function compartirTablero($tableroId, $userIds)
    {
        $tablero = Tablero::findOrFail($tableroId);
        
        if (!$tablero->isOwnedBy($this->id)) {
            throw new \Exception('No tienes permisos para compartir este tablero');
        }
        
        $tablero->usuariosCompartidos()->attach($userIds);
    }
}