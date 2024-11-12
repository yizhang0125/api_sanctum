<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'detail' => $product->detail,
                    'image_url' => $product->image_path ? asset('storage/' . $product->image_path) : null, // Include image URL 
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            }),
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }
}
