<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecioHome extends Model
{
    use HasFactory;
    
    protected $table = 'precios_home';
    
    protected $fillable = [
        'precio_mensual',
        'precio_anual',
        'categoria',
        'nombre_plan',
        'descripcion',
        'destacado',
        'activo',
        'caracteristicas'
    ];
    
    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'precio_anual' => 'decimal:2',
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'caracteristicas' => 'array'
    ];
    
    /**
     * Obtener todos los planes activos
     */
    public static function getPlanesActivos()
    {
        return self::where('activo', true)->orderBy('precio_mensual')->get();
    }
    
    /**
     * Obtener un plan específico por categoría
     */
    public static function getPlanByCategoria($categoria)
    {
        return self::where('categoria', $categoria)
                   ->where('activo', true)
                   ->first();
    }
}