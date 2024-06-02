<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function complete(string $id)
    {
        $task = Task::find($id);
        if ($task) {
            if (!$task->complete) {
                $task->complete = true;
                if ($task->expired_date < Carbon::now()) {
                    $task->expired = true;
                } else {
                    $task->expired = false;
                }
                $task->save();
                return response()->json(["message" => "Task completed!", "data" => $task]);
            } else {
                return response()->json(["message" => "Task already completed!"]);
            }
        } else {
            return response()->json(["message" => "Task not found!"]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['data' => ['tasks' => Task::all()]]);
    }
    public function uncompletedTasks()
    {
        $task = Task::where('complete', false)->get();
        return response()->json(['data' => ['tasks' => $task]]);
    }
    public function completedTasks()
    {
        $task = Task::where('complete', true)->get();
        return response()->json(['data' => ['tasks' => $task]]);
    }
    public function importantTasks()
    {
        $task = Task::where('important', true)
            ->where('complete', false)->get();
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
            'name' => 'required|string|max:255|unique:tasks,name',
            'description' => 'required|string|max:255',
            'expired_date' => 'required|date',
            'important' => 'boolean',
            'card_id' => 'numeric|exists:cards,id',
        ], [
            'name.unique' => 'The name has already been taken.',
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
            'card_id.exists' => 'The card_id does not exist.'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "Validation error!", 'errors' => $validator->errors()], 422);
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
        $task = Task::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:tasks,name',
            'description' => 'string|max:255',
            'expired_date' => 'date',
            'important' => 'boolean',
            'card_id' => 'numeric|exists:cards,id',
        ], [
            'name.unique' => 'The name has already been taken.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'expired_date.date' => 'The expired_date must be a date.',
            'important.boolean' => 'The important must be a boolean.',
            'card_id.numeric' => 'The card_id must be a number.',
            'card_id.exists' => 'The card_id does not exist.'
        ]);

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
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
