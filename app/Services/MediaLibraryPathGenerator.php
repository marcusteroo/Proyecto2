<?php


namespace App\Services;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaLibraryPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getUserPath($media) . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getUserPath($media) . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getUserPath($media) . '/responsive-images/';
    }
    
    /*
     * Get a consistent path for the user
     */
    protected function getUserPath(Media $media): string
    {
        // Usar el ID del modelo relacionado (usuario) como directorio único
        if ($media->model_id && $media->collection_name === 'avatars') {
            return $media->model_id;
        }
        
        // Para otras colecciones, usar el ID del media
        return $media->id;
    }
}