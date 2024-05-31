<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data' => ['cards' => Card::all()]]);
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
            'name' => 'required|string|max:255|unique:cards,name',
            'description' => 'required|string|max:255',
            'desk_id' => 'numeric|exists:desks,id',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.required' => 'The description field is required.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.',
            'desk_id.exists' => 'The desk_id does not exist.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $created_card = Card::create($request->all());
        return response()->json(["message" => "Card created!", "data" => $created_card], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!Card::find($id)) {
            return response()->json(['message' => 'Card not found'], 404);
        } else
            return response()->json(['data' => ['card' => Card::find($id)]]);
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
        $card = Card::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:cards,name',
            'description' => 'string|max:255',
            'desk_id' => 'numeric|exists:desks,id',
        ], [
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.',
            'desk_id.exists' => 'The desk_id does not exist.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }

        $card->update($request->all());
        return response()->json([
            "message" => "Card updated!",
            "data" => $card
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $card = Card::find($id);
        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }
        $card->delete();
        return response()->json(['message' => 'Card deleted']);
    }
}
