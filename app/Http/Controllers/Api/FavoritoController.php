<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Workflow;
use App\Models\Tablero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FavoritoController extends Controller
{
    /**
     * Obtener todos los favoritos del usuario autenticado
     */
    public function index()
    {
        $workflows = Auth::user()->workflowsFavoritos()
            ->select('workflows.*')
            ->with('favoritos')
            ->get()
            ->map(function($workflow) {
                $workflow->tipo = 'workflow';
                $workflow->is_favorite = true;
                return $workflow;
            });
            
        $tableros = Auth::user()->tablerosFavoritos()
            ->select('tableros.*')
            ->with('favoritos')
            ->get()
            ->map(function($tablero) {
                $tablero->tipo = 'tablero';
                $tablero->is_favorite = true;
                return $tablero;
            });
            
        return response()->json([
            'workflows' => $workflows,
            'tableros' => $tableros,
            'total' => $workflows->count() + $tableros->count()
        ]);
    }

    /**
     * Marcar/desmarcar un elemento como favorito
     */
    public function toggleFavorito(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:workflow,tablero',
            'id' => 'required|integer'
        ]);
        
        $tipo = $request->tipo;
        $id = $request->id;
        $userId = Auth::id();
        
        try {
            if ($tipo === 'workflow') {
                $elemento = Workflow::findOrFail($id);
                $modelClass = Workflow::class;
            } else {
                $elemento = Tablero::findOrFail($id);
                $modelClass = Tablero::class;
            }

            // Verificar si ya es favorito
            $favorito = Favorito::where('user_id', $userId)
                ->where('favorable_id', $id)
                ->where('favorable_type', $modelClass)
                ->first();
            
            if ($favorito) {
                // Si ya es favorito, eliminarlo
                $favorito->delete();
                $isFavorito = false;
                $mensaje = 'Elemento eliminado de favoritos';
            } else {
                // Si no es favorito, crearlo
                Favorito::create([
                    'user_id' => $userId,
                    'favorable_id' => $id,
                    'favorable_type' => $modelClass,
                    'fecha_marcado' => now()
                ]);
                $isFavorito = true;
                $mensaje = 'Elemento añadido a favoritos';
            }
            
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorito,
                'message' => $mensaje
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Elemento no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Verificar si un elemento es favorito
     */
    public function isFavorito(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:workflow,tablero',
            'id' => 'required|integer'
        ]);
        
        $tipo = $request->tipo;
        $id = $request->id;
        $userId = Auth::id();
        
        $modelClass = $tipo === 'workflow' ? Workflow::class : Tablero::class;
        
        $isFavorito = Favorito::where('user_id', $userId)
            ->where('favorable_id', $id)
            ->where('favorable_type', $modelClass)
            ->exists();
        
        return response()->json(['is_favorite' => $isFavorito]);
    }
}