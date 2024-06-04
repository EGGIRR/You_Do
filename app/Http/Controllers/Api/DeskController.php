<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeskController extends Controller
{
    public function index()
    {
        $desks = Desk::select('desks.id','desks.name','desks.description')
        ->where('user_id', Auth::id())->get();
        return response()->json(['data' => ['desks' => $desks]]);
    }

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
