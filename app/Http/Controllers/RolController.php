<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Rol::all();
        return view('admin.roles', compact('roles'));
    }

    public function getRoles()
    {
        $roles = Rol::all();
        return response()->json($roles);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rolName' => 'required|string|max:255|unique:rol,rolName',
        ]);

        Rol::create([
            'rolName' => $request->rolName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rol = Rol::findOrFail($id);
        if(!$rol){
            return response()->json([
                'success' => false,
                'message' => 'Role not found',
            ], 404);
        }
        $request->validate([
            'rolName' => 'required|string|max:255|unique:rol,rolName,'.$rol->id,
        ]);

        $rol->rolName = $request->rolName;
        $rol->save();
        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
