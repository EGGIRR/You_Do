<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desk;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function complete(string $id)
    {
        $mytasks = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }

        $task = Task::find($id);

        if (!$task) {
            return response()->json(["message" => "Task not found!"], 404);
        }

        if ($task->complete) {
            return response()->json(["message" => "Task already completed!"]);
        }

        $task->complete = true;
        $task->expired = $task->expired_date < Carbon::now();
        $task->save();

        return response()->json(["message" => "Task completed!", "data" => $task]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->get();
        return response()->json(['data' => ['tasks' => $tasks]]);
    }
    public function uncompletedTasks()
    {
        $task = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->where('tasks.complete', false)
            ->get();
        return response()->json(['data' => ['tasks' => $task]]);

    }
    public function completedTasks()
    {
        $task = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->where('tasks.complete', true)
            ->get();
        return response()->json(['data' => ['tasks' => $task]]);
    }
    public function importantTasks()
    {
        $task = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->where('tasks.complete', false)
            ->where('tasks.important', true)
            ->get();
        return response()->json(['data' => ['tasks' => $task]]);
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
            'expired_date' => 'required|date',
            'important' => 'boolean',
            'card_id' => 'numeric|exists:cards,id',
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.required' => 'The description field is required.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'expired_date.required' => 'The expired_date field is required.',
            'expired_date.date' => 'The expired_date must be a date.',
            'important.boolean' => 'The important must be a boolean.',
            'card_id.numeric' => 'The card_id must be a number.',
            'card_id.exists' => 'The card_id does not exist or does not belong to the current user.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $cards = DB::table('cards')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('cards.*', 'desks.user_id')
            ->where('desks.user_id', Auth::user()->id)
            ->where('cards.id', $request->input('card_id'))
            ->first();

        if (!$cards) {
            return response()->json(["message" => "Card is not yours!"], 403);
        }

        $created_desk = Task::create($request->all());
        return response()->json(["message" => "Task created!", "data" => $created_desk], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!Task::find($id)) {
            return response()->json(['message' => 'Task not found'], 404);
        } else
            return response()->json(['data' => ['task' => Task::find($id)]]);
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
        $mytasks = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }
        $task = Task::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string|max:255',
            'expired_date' => 'date',
            'important' => 'boolean',
            'card_id' => 'numeric|exists:cards,id',
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'expired_date.date' => 'The expired_date must be a date.',
            'important.boolean' => 'The important must be a boolean.',
            'card_id.numeric' => 'The card_id must be a number.',
            'card_id.exists' => 'The card_id does not exist.'
        ]);
        $cards = DB::table('cards')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('cards.*', 'desks.user_id')
            ->where('desks.user_id', Auth::user()->id)
            ->where('cards.id', $request->input('card_id'))
            ->first();

        if (!$cards) {
            return response()->json(["message" => "Card is not yours!"], 403);
        }
        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
        }
        $task->update($request->all());

        return response()->json(["message" => "Task updated!", "data" => $task]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mytasks = DB::table('tasks')
            ->join('cards', 'tasks.card_id', '=', 'cards.id')
            ->join('desks', 'cards.desk_id', '=', 'desks.id')
            ->select('tasks.*')
            ->where('desks.user_id', Auth::user()->id)
            ->pluck('id')
            ->all();

        if (!in_array($id, $mytasks)) {
            return response()->json(["message" => "Task not found in your tasks!"], 404);
        }
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
