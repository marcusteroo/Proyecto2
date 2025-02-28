<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tablero extends Model
{
    use HasFactory;
    protected $table = 'tableros'; 
    protected $primaryKey = 'id_tablero'; 

    public $incrementing = false; 

    protected $keyType = 'int';

    protected $fillable = ['id_tablero', 'nombre', 'id_creador'];
    public function tablero()
    {
        return $this->belongsTo(User::class, 'id_creador');
    }
}
