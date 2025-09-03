<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        // ? where
        $category = Category::query(); // ORM eloquent
        if ($req->has("search")) {
            // $category->where("name", "=", $req->input("search"));
            $category->where("name", "LIKE", "%" . $req->input("search") . "%");
        }
        if ($req->has("status")) {
            $category->where("status", "=", $req->input("status"));
        }
        $list = $category->get();
        return response()->json([
            "list" => $list,
            "query" => $req->input("search")
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|boolean',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer'
        ]);
        $cat = Category::create($validated);
        // $cat = new Category();
        // $cat->fill($validated);
        // $cat->description = $request->input("description");
        // $cat->parent_id = $request->input("parent_id");
        $cat->save();
        return [
            "data" => $cat,
            "message" => "Save success"
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json([
            "data" => Category::find($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return Category::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return [
                "error" => true,
                "message" => "Date not found!"
            ];
        } else {
            $validation = $request->validate([
                'name' => 'required|string',
                'status' => 'required|boolean',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|integer'
            ]);
            $cat->name = $request->input("name");
            $cat->description = $request->input("description");
            $cat->status = $request->input("status");
            $cat->parent_id = $request->input("parent_id");
            $cat->update($validation);
            return [
                "data" => $cat,
                "message" => "update success"
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            return [
                "error" => true,
                "message" => "Date not found!"
            ];
        } else {
            $cat->delete();
            return [
                "data" => $cat,
                "message" => "Delete success"
            ];
        }
    }
}
