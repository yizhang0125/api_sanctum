<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Validator;
use App\Http\Resources\ProductResource as ProductResource;

class ProductController extends BaseController
{
    public function index(): JsonResponse
    {
        $products = Product::all();
    
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-images', 'public');
            $input['image_path'] = $imagePath; // Save the image path in the 'image_path' field
        }

        $product = Product::create($input);

        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }

    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    // Validation and image handling logic here

    $product->name = $request->input('name');
    $product->detail = $request->input('detail');

    // Save the new image if uploaded
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('product-images', 'public');
        $product->image_url = $imagePath;
    }

    $product->save();

    return response()->json(['success' => true, 'message' => 'Product updated successfully']);
}
    
    

    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        // Delete the image from storage if it exists
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
