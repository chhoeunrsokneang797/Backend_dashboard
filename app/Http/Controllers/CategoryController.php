<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "list" => Category::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cat = new Category();
        $cat->name = $request->input("name");
        $cat->description = $request->input("description");
        $cat->status = $request->input("status");
        $cat->parent_id = $request->input("parent_id");
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
            $cat->name = $request->input("name");
            $cat->description = $request->input("description");
            $cat->status = $request->input("status");
            $cat->parent_id = $request->input("parent_id");
            $cat->update();
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
