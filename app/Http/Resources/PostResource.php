<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user'),
            'categories' => $this->whenLoaded('categories'),
            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    // Añade esta línea para el log
                    \Log::info('Media URL generated: ' . $media->getUrl());
                    
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'original_url' => asset($media->getUrl()),
                        'thumbnail_url' => $media->hasGeneratedConversion('resized-image') 
                            ? asset($media->getUrl('resized-image')) 
                            : asset($media->getUrl()),
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}