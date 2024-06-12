<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRequests\TaskRequests\StoreTaskRequest;
use App\Http\Requests\ApiRequests\TaskRequests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $task->complete();
        $task->expire();

        return response()->json(["message" => "Task completed!", "data" => $task]);
    }

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

    public function taskImportant(string $id)
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
        $task->important();
        if ($task->important) {
            return response()->json(["message" => "Task in favorites!", "data" => $task]);
        } else {
            return response()->json(["message" => "Task deleted from favorites!", "data" => $task]);
        }

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

    public function store(StoreTaskRequest $request)
    {

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

    public function update(UpdateTaskRequest $request, string $id)
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
        if ($request->card_id) {
            $cards = DB::table('cards')
                ->join('desks', 'cards.desk_id', '=', 'desks.id')
                ->select('cards.*', 'desks.user_id')
                ->where('desks.user_id', Auth::user()->id)
                ->where('cards.id', $request->input('card_id'))
                ->first();
            if (!$cards) {
                return response()->json(["message" => "Card is not yours!"], 403);
            }
        }

        $task->update($request->all());

        return response()->json(["message" => "Task updated!", "data" => $task]);
    }

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
