<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        // ? where
        $province = Province::query(); // ORM eloquent
        if ($req->has("search")) {
            // $category->where("name", "=", $req->input("search"));
            $province->where("name", "LIKE", "%" . $req->input("search") . "%");
        }
        if ($req->has("status")) {
            $province->where("status", "=", $req->input("status"));
        }
        $list = $province->get();
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
        $validation = $request->validate([
            "name" => "required|string",
            "code" => "required|string",
            "description" => "nullable|string",
            "distand_from_city" => "required|numeric",
            "status" => "required|boolean"
        ]);
        $data = Province::create($validation);
        return response()->json([
            "data" => $data,
            "message" => "Insert success"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json([
            "data" => Province::find($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validation = $request->validate([
            "name" => "required|string",
            "code" => "required|string",
            "description" => "nullable|string",
            "distand_from_city" => "required|numeric",
            "status" => "required|boolean"
        ]);

        $data = Province::find($id);

        if (!$data) {
            return response()->json([
                "error" => [
                    "update" => "Data not Found!"
                ]
            ], 404); // Important: Use 404 for "Not Found" errors
        }

        $data->update($validation); // This single line is sufficient

        return response()->json([
            "data" => $data,
            "message" => "update success"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Province::find($id);
        if (!$data) {
            return response()->json([
                "error" => [
                    "delete" => "Data not Found!"
                ]
            ]);
        } else {
            $data->delete();
            return response()->json([
                "message" => "Delete success"
            ]);
        }
    }
}
