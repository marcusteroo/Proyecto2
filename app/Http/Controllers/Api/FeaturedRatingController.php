<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FeaturedRatingController extends Controller
{
    /**
     * Obtener reseñas destacadas para la página de inicio
     */
    public function getFeaturedForHome()
    {
        $ratings = Rating::with('user')
                    ->where('featured', true)
                    ->orderBy('featured_order')
                    ->limit(6)
                    ->get()
                    ->map(function($rating) {
                        // Obtener foto del usuario si no hay photo_path
                        $photoPath = $rating->photo_path;
                        
                        if (!$photoPath && $rating->user && $rating->user->avatar) {
                            $photoPath = $rating->user->avatar;
                        }
                        
                        // Si no hay foto, usar un placeholder
                        if (!$photoPath) {
                            $photoPath = '/images/testimonials/placeholder.webp';
                        }
                        
                        // Convertir categories a array 
                        $categoriesArray = [];
                        if ($rating->categories) {
                            // Si ya es JSON, decodificarlo. Si no, dividirlo por comas.
                            if ($this->isJson($rating->categories)) {
                                $categoriesArray = json_decode($rating->categories);
                            } else {
                                $categoriesArray = explode(',', $rating->categories);
                                // Eliminar espacios en blanco
                                $categoriesArray = array_map('trim', $categoriesArray);
                            }
                        }
                        
                        return [
                            'nombre' => $rating->user->name,
                            'position' => $rating->job_position,
                            'company' => $rating->company,
                            'foto' => $photoPath,
                            'texto' => $rating->comment,
                            'rating' => number_format($rating->score, 1),
                            'verified' => true,
                            'featured' => $rating->featured,
                            'tags' => $categoriesArray // Mantener la clave 'tags' para compatibilidad con el frontend
                        ];
                    });

        return response()->json([
            'success' => true,
            'data' => $ratings
        ]);
    }
    
    // Método para verificar si una cadena es JSON válido
    private function isJson($string) {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Obtener todas las reseñas
     */
    public function index()
    {
        $ratings = Rating::with('user')
                    ->orderBy('featured', 'desc')
                    ->orderBy('featured_order')
                    ->get()
                    ->map(function($rating) {
                        // Procesar la foto del usuario
                        $photoPath = $rating->photo_path;
                        
                        if (!$photoPath && $rating->user && $rating->user->avatar) {
                            $photoPath = $rating->user->avatar;
                        }
                        
                        // Convertir categories a array para mostrar 
                        $categoriesArray = [];
                        if ($rating->categories) {
                            if ($this->isJson($rating->categories)) {
                                $categoriesArray = json_decode($rating->categories);
                            } else {
                                $categoriesArray = explode(',', $rating->categories);
                                $categoriesArray = array_map('trim', $categoriesArray);
                            }
                        }
                        
                        // Registrar para depuración
                        Log::debug("Categorías procesadas para rating id {$rating->id}", [
                            'categories_raw' => $rating->categories,
                            'categories_procesados' => $categoriesArray
                        ]);
                        
                        // Devolver el objeto con datos procesados
                        return array_merge($rating->toArray(), [
                            'photo_path' => $photoPath,
                            'tags' => $categoriesArray // Mantener la clave 'tags' para compatibilidad con el frontend existente
                        ]);
                    });

        return response()->json([
            'success' => true,
            'data' => $ratings
        ]);
    }

    /**
     * Marcar/desmarcar una reseña como destacada
     */
    public function updateFeaturedStatus(Request $request, $id)
    {
        $rating = Rating::findOrFail($id);
        $rating->featured = $request->featured;
        
        // Si se está destacando la reseña, darle el último orden
        if ($rating->featured) {
            $lastFeatured = Rating::where('featured', true)->max('featured_order');
            $rating->featured_order = ($lastFeatured ?? 0) + 1;
        }
        
        $rating->save();
        
        return response()->json([
            'success' => true,
            'data' => $rating,
            'message' => $rating->featured ? 'Reseña destacada correctamente' : 'Reseña eliminada de destacados'
        ]);
    }
    
    /**
     * Actualizar el orden de las reseñas destacadas
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ratings' => 'required|array',
            'ratings.*.id' => 'required|exists:ratings,id',
            'ratings.*.featured_order' => 'required|integer|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de ordenación incorrectos',
                'errors' => $validator->errors()
            ], 422);
        }
        
        foreach ($request->ratings as $ratingData) {
            $rating = Rating::find($ratingData['id']);
            $rating->featured_order = $ratingData['featured_order'];
            $rating->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado correctamente'
        ]);
    }
}