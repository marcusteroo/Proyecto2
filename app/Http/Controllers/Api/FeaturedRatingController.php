<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                        
                        // Convertir categories de string separado por comas a array
                        $categoriesArray = $rating->categories ? explode(',', $rating->categories) : [];
                        
                        return [
                            'nombre' => $rating->user->name,
                            'position' => $rating->job_position,
                            'company' => $rating->company,
                            'foto' => $photoPath,
                            'texto' => $rating->comment,
                            'rating' => number_format($rating->score, 1),
                            'verified' => true,
                            'featured' => $rating->featured,
                            'tags' => $categoriesArray // Enviar las categorías como array
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
                        
                        // Convertir categories a array para mostrar como tags
                        $tags = [];
                        if ($rating->categories) {
                            if ($this->isJson($rating->categories)) {
                                $tags = json_decode($rating->categories);
                            } else {
                                $tags = explode(',', $rating->categories);
                                $tags = array_map('trim', $tags);
                            }
                        }
                        
                        // Devolver el objeto con datos procesados
                        return array_merge($rating->toArray(), [
                            'photo_path' => $photoPath,
                            'tags' => $tags
                        ]);
                    });

        return response()->json([
            'success' => true,
            'data' => $ratings
        ]);
    }

    /**
     * Actualizar estado destacado y orden de una reseña
     */
    public function updateFeaturedStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'featured' => 'required|boolean',
            'featured_order' => 'nullable|integer|min:1|max:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $rating = Rating::findOrFail($id);
        
        // Si estamos activando el destacado y ya hay 6 reseñas destacadas
        if ($request->featured && !$rating->featured) {
            $featuredCount = Rating::where('featured', true)->count();
            if ($featuredCount >= 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe el máximo de 6 reseñas destacadas. Desactiva alguna antes de añadir esta.'
                ], 422);
            }
        }

        $rating->featured = $request->featured;
        
        // Si se está activando y no tiene orden, asignar el último
        if ($request->featured && !$rating->featured_order) {
            if (!$request->featured_order) {
                $lastOrder = Rating::where('featured', true)->max('featured_order') ?? 0;
                $rating->featured_order = $lastOrder + 1;
            } else {
                // Reordenar si el orden solicitado ya está ocupado
                if (Rating::where('featured', true)
                        ->where('id', '!=', $id)
                        ->where('featured_order', $request->featured_order)
                        ->exists()) {
                    // Mover todos los elementos con orden >= al nuevo orden
                    Rating::where('featured', true)
                        ->where('featured_order', '>=', $request->featured_order)
                        ->increment('featured_order');
                }
                $rating->featured_order = $request->featured_order;
            }
        } else if ($request->featured_order) {
            $rating->featured_order = $request->featured_order;
        }
        
        $rating->save();
        
        // Si se desactivó, reordenar las demás
        if (!$request->featured) {
            $this->reorderFeaturedRatings();
        }
        
        return response()->json([
            'success' => true,
            'data' => $rating
        ]);
    }
    
    /**
     * Actualizar orden de las reseñas destacadas
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ratings' => 'required|array',
            'ratings.*.id' => 'required|exists:ratings,id',
            'ratings.*.featured_order' => 'required|integer|min:1|max:6'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Validar que solo haya 6 elementos y no se repitan órdenes
        if (count($request->ratings) > 6) {
            return response()->json([
                'success' => false,
                'message' => 'No puede haber más de 6 reseñas destacadas.'
            ], 422);
        }
        
        $orders = array_column($request->ratings, 'featured_order');
        if (count(array_unique($orders)) !== count($orders)) {
            return response()->json([
                'success' => false,
                'message' => 'Los órdenes no pueden repetirse.'
            ], 422);
        }
        
        foreach ($request->ratings as $ratingData) {
            $rating = Rating::findOrFail($ratingData['id']);
            $rating->featured = true;
            $rating->featured_order = $ratingData['featured_order'];
            $rating->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado correctamente'
        ]);
    }
    
    /**
     * Reordenar las reseñas destacadas para evitar huecos
     */
    private function reorderFeaturedRatings()
    {
        $featuredRatings = Rating::where('featured', true)
                        ->orderBy('featured_order')
                        ->get();
        
        $order = 1;
        foreach ($featuredRatings as $rating) {
            $rating->featured_order = $order++;
            $rating->save();
        }
    }
}