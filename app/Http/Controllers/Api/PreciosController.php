<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrecioHome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreciosController extends Controller
{
    public function index()
    {
        $precios = PrecioHome::orderBy('categoria')->get();
        return response()->json($precios);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'precio_mensual' => 'required|numeric|min:0',
            'precio_anual' => 'required|numeric|min:0',
            'categoria' => 'required|in:basico,premium,business',
            'nombre_plan' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'destacado' => 'boolean',
            'caracteristicas' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Si el plan está marcado como destacado, desmarcamos cualquier otro plan
        if ($request->destacado) {
            PrecioHome::where('destacado', true)->update(['destacado' => false]);
        }
        
        // Si el plan está activo, desactivamos cualquier otro plan con la misma categoría
        if ($request->has('activo') && $request->activo) {
            PrecioHome::where('categoria', $request->categoria)
                      ->where('activo', true)
                      ->update(['activo' => false]);
        }
        
        $precio = PrecioHome::create($request->all());
        return response()->json($precio, 201);
    }
    
    public function show($id)
    {
        $precio = PrecioHome::findOrFail($id);
        return response()->json($precio);
    }
    
    public function update(Request $request, $id)
    {
        $precio = PrecioHome::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'precio_mensual' => 'required|numeric|min:0',
            'precio_anual' => 'required|numeric|min:0',
            'categoria' => 'required|in:basico,premium,business',
            'nombre_plan' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'destacado' => 'boolean',
            'caracteristicas' => 'nullable|array'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Si el plan está marcado como destacado, desmarcamos cualquier otro plan
        if ($request->destacado && !$precio->destacado) {
            PrecioHome::where('destacado', true)->update(['destacado' => false]);
        }
        
        // Si el plan está activo y cambia de categoría, desactivamos cualquier otro con la nueva categoría
        if ($request->has('activo') && $request->activo && $request->categoria != $precio->categoria) {
            PrecioHome::where('categoria', $request->categoria)
                      ->where('activo', true)
                      ->update(['activo' => false]);
        }
        
        $precio->update($request->all());
        return response()->json($precio);
    }
    
    public function destroy($id)
    {
        $precio = PrecioHome::findOrFail($id);
        $precio->delete();
        return response()->json(null, 204);
    }
    
    // Método para obtener los precios para el frontend
    public function getPrecios()
    {
        $precios = PrecioHome::where('activo', true)
                             ->orderBy('precio_mensual')
                             ->get();
                             
        return response()->json($precios);
    }
}