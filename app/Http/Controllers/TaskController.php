<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Task;
use App\Models\TaskUser;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user=auth()->user();
        $tasksCreated=$user->tasksCreated;
        $tasksAssgined=$user->assignedTasks;
        $allTasks = $tasksCreated->merge($tasksAssgined);
        return response()->json($allTasks,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'string|required',
            'description' => 'string|required',
            'dead_line' => 'date|required',
        ]);
    
        // Create a new task instance
        $task = new Task();
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->dead_line = $validated['dead_line'];
        $task->user_id = auth()->user()->id;
    
        // Check if group_id is present and valid
        if ($request->has('group_id')) {
            $group = Group::find($request->input('group_id'));
    
            if (!$group) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'group does not exist'
                ], 404);
            }
    
            $task->group_id = $group->id; // Associate group with task
        }
    
        // Save the task only after validating group_id
        $task->save();
    
        // Assign users to the task if group_id is present and users are provided
        if ($request->has('members') && $task->group) {
            foreach ($request->input('members') as $user_id) {
                // Check if the user is a member of the group
                if (!($task->group->members->contains('id', $user_id))) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'user is not a part of the group'
                    ], 404);
                }
            }
            $task->assignedUsers()->attach($request->input('members'));
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'task created successfully'
        ], 201);
    }
    

    /**
     * Display the specified resource.
     */
    public function soloTasks()
    {
        $user=auth()->user();
        $tasksCreated=$user->tasksCreated;
        $tasksCreated = $user->tasksCreated->filter(function($task) {
            return is_null($task->group_id);
        })->values();
        return response()->json($tasksCreated,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $task=Task::where('id',$id)->first();
        $task->title=$request->input('title');
        $task->description=$request->input('description');
        $task->dead_line=$request->input('dead_line');
        $task->save();
        return response()->json([
            'status'=>'success',
            'message'=>'updated successfully'
        ],202);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task=Task::where('id',$id)->first();
        if($task->user_id==auth()->user()->id){
            $task->delete();
            return response()->json([
                'status'=>'success',
                'message'=>'deleted successfully'
            ],202);
        }else{
            return response()->json([
                'status'=>'failed',
                'message'=>'a task can only be deleted by the owner'
            ],202);
        }
    }
}
