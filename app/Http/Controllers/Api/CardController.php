<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\CardRequests\StoreCardRequest;
use App\Http\Requests\ApiRequests\CardRequests\UpdateCardRequest;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;

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

    public function store(StoreCardRequest $request)
    {
        $created_card = Card::create($request->all());
        return response()->json(["message" => "Card created!", "data" => $created_card], 201);
    }

    public function update(UpdateCardRequest $request, string $id)
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

        $card->update($request->all());
        return response()->json([
            "message" => "Card updated!",
            "data" => $card
        ]);
    }

    public function destroy(string $id)
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
        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }
        $card->delete();
        return response()->json(['message' => 'Card deleted']);
    }
}
