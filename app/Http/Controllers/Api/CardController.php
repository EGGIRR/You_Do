<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('cards.*')
            ->where('desks.user_id', Auth::user()->id)
            ->get();

        return response()->json(['data' => ['cards' => $cards]]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'desk_id' => 'numeric|exists:desks,id,user_id,' . Auth::user()->id,
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.required' => 'The description field is required.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.',
            'desk_id.exists' => 'The desk_id does not exist or does not belong to the current user.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $created_card = Card::create($request->all());
        return response()->json(["message" => "Card created!", "data" => $created_card], 201);
    }

    public function update(Request $request, string $id)
    {
        $mycards = Card::join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('cards.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mycards)) {
            return response()->json(["message" => "Card not found in your cards!"], 404);
        }
        $card = Card::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string|max:255',
            'desk_id' => 'numeric|exists:desks,id,user_id,' . Auth::user()->id,
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'desk_id.numeric' => 'The desk_id must be a number.',
            'desk_id.exists' => 'The desk_id does not exist or does not belong to the current user.'
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
