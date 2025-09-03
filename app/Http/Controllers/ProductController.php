<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Check for category_id filter
        if ($request->has("category_id")) {
            $query->where("category_id", "=", $request->input("category_id"));
        }

        // Check for brand_id filter
        if ($request->has("brand_id")) {
            $query->where("brand_id", "=", $request->input("brand_id"));
        }

        // Check for search filter (name)
        if ($request->has("search")) {
            // FIX: Corrected search column from 'product' to 'product_name'
            $query->where("product_name", "LIKE", "%" . $request->input("search") . "%");
        }

        // Check for status filter
        if ($request->has("status")) {
            $query->where("status", "=", $request->input("status"));
        }

        // Apply the filters and load relationships before getting the results
        $products = $query->with(['category', 'brand'])->get();

        return response()->json([
            "list" => $products,
            "category" => Category::all(),
            "brand" => Brand::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // FIX: The 'image' field validation is changed to required for new products.
        // This prevents creating records with a null image field.
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_name' => 'required|string',
            'description' => 'nullable|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        return response()->json([
            "data" => $product,
            "message" => "Data created successfully"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        // FIX: Added error handling for products not found
        if (!$product) {
            return response()->json(["message" => "Product not found."], 404);
        }

        return response()->json([
            "data" => $product->load(['category', 'brand'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the product by its ID
        $product = Product::find($id);

        // Check if the product was found
        if (!$product) {
            return response()->json([
                "message" => "Product not found."
            ], 404);
        }

        // Validate the request data
        // FIX: 'image' is now nullable for updates
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'product_name' => 'required|string',
            'description' => 'nullable|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        $data = $request->all();

        // FIX: Handle image replacement/deletion
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->input('image_remove')) {
            // Logic to remove the image if a specific flag is sent from the frontend
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null; // Set image to null in the database
        } else {
            // If no new image is uploaded and no image_remove flag,
            // keep the existing image
            unset($data['image']);
        }

        // Update the product with the validated data
        $product->update($data);

        return response()->json([
            "data" => $product,
            "message" => "Data updated successfully"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        // FIX: Added error handling for products not found
        if (!$product) {
            return response()->json(["message" => "Product not found."], 404);
        }

        // FIX: Check if image exists before trying to delete it
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json([
            "message" => "Data deleted successfully"
        ], 200);
    }
}
