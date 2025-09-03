<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * BrandController handles operations related to brands, including creation.
 */
class BrandController extends Controller
{
    /**
     * Store a newly created brand in storage.
     * This method handles the incoming request and performs validation.
     * If validation fails, Laravel automatically returns a 422 response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data.
            // This is the most important part for preventing a 422 error.
            // Laravel's validator will automatically check against the rules.
            // The 'unique:brands' rule here directly correlates to your migration.
            $validatedData = $request->validate([
                'name'         => 'required|string|max:255',
                'code'         => 'required|string|max:255|unique:brands,code',
                'from_country' => 'required|string|max:255',
                'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status'       => 'required|in:active,inactive',
            ]);

            // If an image was uploaded, store it and update the validated data with the path.
            if ($request->hasFile('image')) {
                // Store the image in the 'public' disk under a 'brands' directory.
                // The filename will be a unique hash.
                $imagePath = $request->file('image')->store('brands', 'public');
                $validatedData['image'] = $imagePath;
            }

            // Create a new Brand model instance with the validated data.
            $brand = Brand::create($validatedData);

            // Return a JSON response with a success message and the created brand.
            return response()->json([
                'message' => 'Brand created successfully!',
                'brand'   => $brand,
            ], 201); // 201 Created status code
        } catch (ValidationException $e) {
            // Laravel's validate() method automatically handles this,
            // but this catch block is good practice for other potential errors.
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle any other general exceptions
            return response()->json([
                'message' => 'An error occurred while creating the brand.',
            ], 500); // 500 Internal Server Error
        }
    }
}
