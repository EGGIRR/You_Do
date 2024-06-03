<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desks = Desk::where('user_id', Auth::id())->get();
        return response()->json(['data' => ['desks' => $desks]]);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.required' => 'The description field is required.',
            'description.max' => 'The description may not be greater than 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $created_desk = Desk::create($data);
        return response()->json(["message" => "Desk created!", "data" => $created_desk], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!Desk::find($id)) {
            return response()->json(['message' => 'Desk not found'], 404);
        }else
        return response()->json(['data' => ['desk' => Desk::find($id)]]);
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
        $desk = Desk::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string|max:255',
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $desk->update($request->all());

        return response()->json(["message" => "Desk updated!", "data" => $desk]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $desk = Desk::find($id);
        if (!$desk) {
            return response()->json(['message' => 'Desk not found'], 404);
        }
        $desk->delete();
        return response()->json(['message' => 'Desk deleted']);
    }
}
