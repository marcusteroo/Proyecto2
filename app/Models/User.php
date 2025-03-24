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
        $this->addMediaCollection('images/users')
            ->useFallbackUrl('/images/placeholder.jpg')
            ->useFallbackPath(public_path('/images/placeholder.jpg'));
    }

    public function registerMediaConversions(Media $media = null): void
    {
        if (env('RESIZE_IMAGE') === true) {

            $this->addMediaConversion('resized-image')
                ->width(env('IMAGE_WIDTH', 300))
                ->height(env('IMAGE_HEIGHT', 300));
        }
    }
    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'usuarios_workflows', 'user_id', 'workflow_id')
                ->withPivot('rol', 'fecha_compartido')
                ->withTimestamps();
    }
    public function ownedWorkflows()
{
    return $this->workflows()->wherePivot('rol', 'propietario');
}
public function sharedWorkflows()
{
    return $this->workflows()->wherePivot('rol', '!=', 'propietario');
}
public function tablerosCompartidos()
{
    return $this->belongsToMany(Tablero::class, 'usuarios_tableros', 'user_id', 'tablero_id')
                ->withPivot('fecha_compartido')
                ->withTimestamps();
}
public function favoritos()
{
    return $this->hasMany(Favorito::class);
}
public function workflowsFavoritos()
{
    return $this->belongsToMany(Workflow::class, 'favoritos', 'user_id', 'favorable_id')
            ->where('favorable_type', Workflow::class)
            ->withTimestamps();
}
public function tablerosFavoritos()
{
    return $this->belongsToMany(Tablero::class, 'favoritos', 'user_id', 'favorable_id')
            ->where('favorable_type', Tablero::class)
            ->withTimestamps();
}
}
