<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\DeskRequests\StoreDeskRequest;
use App\Http\Requests\ApiRequests\DeskRequests\UpdateDeskRequest;
use App\Models\Desk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeskController extends Controller
{
    public function index()
    {
        $desks = Desk::select('desks.id', 'desks.name', 'desks.description')
            ->where('user_id', Auth::id())->get();
        return response()->json(['data' => ['desks' => $desks]]);
    }

    public function store(StoreDeskRequest $request)
    {
        $request['user_id'] = Auth::id();
        $created_desk = Desk::create($request->all());
        return response()->json(["message" => "Desk created!", "data" => $created_desk], 201);
    }

    public function update(UpdateDeskRequest $request, string $id)
    {
        $mydesks = DB::table('desks')
            ->select('desks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mydesks)) {
            return response()->json(["message" => "Desk not found in your tasks!"], 404);
        }
        $desk = Desk::find($id);

        $desk->update($request->all());

        return response()->json(["message" => "Desk updated!", "data" => $desk]);
    }

    public function destroy(string $id)
    {
        $mydesks = DB::table('desks')
            ->select('desks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mydesks)) {
            return response()->json(["message" => "Desk not found in your desks!"], 404);
        }
        $desk = Desk::find($id);
        if (!$desk) {
            return response()->json(['message' => 'Desk not found'], 404);
        }
        $desk->delete();
        return response()->json(['message' => 'Desk deleted']);
    }
}
