<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve all brands from the database
        $query = Brand::query();
        if ($request->has("search")) {
            $query->where("name", "like", "%" . $request->search . "%");
        }
        if ($request->has("status")) {
            $query->where("status", "=", $request->status);
        }

        $brands = $query->get();
        // Return a JSON response with the list of brands
        return response()->json([           
            "list" => $brands
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:brands,code',
            'from_country' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max size is 2048 KB (2MB)
        ]);

        // Handle the image upload if a file is present in the request
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('brands', 'public');
        }

        // Create a new Brand model instance with the validated data
        $brand = Brand::create([
            'name' => $request->name,
            'code' => $request->code,
            'from_country' => $request->from_country,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        // Return a success JSON response
        return response()->json([
            "data" => $brand,
            "message" => "Data created successfully"
        ], 201); // 201 Created is more semantically correct for a store operation
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the brand by its ID. If not found, return a 404 response.
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                "message" => "Brand not found"
            ], 404);
        }

        // Return the brand data in a JSON response
        return response()->json([
            "data" => $brand
        ], 200);
    }


    public function update(Request $request, string $id)
    {
        // Find the brand by its ID. If not found, return a 404 response.
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                "message" => "Brand not found"
            ], 404);
        }

        // Validate the incoming request data, ignoring the current record's 'code' for uniqueness
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:brands,code,' . $id,
            'from_country' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max size is 2048 KB (2MB)
            'image_remove' => 'nullable|string'
        ]);

        // Initialize $imagePath to the current brand's image path
        $imagePath = $brand->image;

        // Handle the image update
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            $imagePath = $request->file('image')->store('brands', 'public');
            $brand->image = $imagePath;
        } else if ($request->image_remove != " ") {
            // Delete the old image if it exists and a remove flag is sent
            if ($brand->image) {
                Storage::disk('public')->delete($brand->image);
            }
            // Set the image path to null
            $imagePath = null;
        }

        // Update the brand with the new data
        $brand->update([
            'name' => $request->name,
            'code' => $request->code,
            'from_country' => $request->from_country,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        // Return a success JSON response
        return response()->json([
            'image_remove' => $request->image_remove,
            "data" => $brand,
            "message" => "Data updated successfully"
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the brand by its ID. If not found, return a 404 response.
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                "message" => "Brand not found"
            ], 404);
        }

        // Delete the image associated with the brand if it exists
        if ($brand->image) {
            Storage::disk('public')->delete($brand->image);
        }

        // Delete the brand model instance
        $brand->delete();

        // Return a success JSON response
        return response()->json([
            "data" => $brand,
            "message" => "Data deleted successfully"
        ], 200);
    }
}
