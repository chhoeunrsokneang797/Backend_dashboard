<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "total" => 100,
            "list" => Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->input('key_nmae')
        // $request->input() // get all json data

        $validated = $request->validate([
            'name' => 'required|string|max:225',
            'code' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|boolean',
            'test' => 'required|string',
        ]);
        Role::create($validated);
        return [
            // "data" => $role,
            "message" => "Role created successfully"
        ];



        // $role = new Role();
        // $role->name = $request->input('name');
        // $role->code = $request->input('code');
        // $role->description = $request->input('description');
        // $role->status = $request->input('status'); // default status is 1
        // $role->test = $request->input('test');
        // $role->save();
        // return [
        //     "data" => $role,
        //     "message" => "Role created successfully"
        // ];
    }

    /**
     * Display the specified resource.
     * @param int $id
     * return \\Illuminate\Http\Response
     */
    public function show($id)
    {
        return  Role::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return [
                "error" => true,
                "message" => "Data not found!"
            ];
        } else {
            $role->name = $request->input("name");
            $role->code = $request->input("code");
            $role->description = $request->input("description");
            $role->status = $request->input("status");
            $role->test = $request->input("Testing");
            $role->update();
            return [
                "data" => $role,
                "message" => "intert successfully!"
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return [
                "error" => true,
                "message" => "Data not found!"
            ];
        } else {
            $role->delete();
            return [
                "data" => $role,
                "message" => "Data deleted successfull!"
            ];
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return [
                "error" => true,
                "message" => "Data not found!"
            ];
        } else {
            $role->status = $request->input("status");
            $role->update();
            return [
                "data" => $role,
                "message" => "Data change to status (" . $role->status . ") success"
            ];
        }
    }
}
