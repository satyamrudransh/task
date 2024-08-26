<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{


   // Display the tasks
   public function index()
   {
       $tasks = Task::paginate(10); // or Task::all() if you don't need pagination
       return response()->json([
           'tasks' => $tasks->items(),
           'hasMore' => $tasks->hasMorePages()
       ]);
   }

   // Store a newly created resource in storage
   public function store(Request $request)
   {
    $request->validate([
        'title' => 'required|string|max:255|unique:task,title',
    ]);

    $task = Task::create([
        'title' => $request->input('title'),
        'is_complete' => false,
    ]);

    return response()->json($task);
   }
   // Mark the specified resource as complete
   public function update(Request $request, $id)
   {
       $task = Task::findOrFail($id);
       
       $request->validate([
           'status' => 'in:pending,complete',
           'title' => [
               'string',
               'max:255',
               Rule::unique('task')->ignore($id) // Ensure the task name is unique, excluding the current task
           ],
       ]);
   
       // Update the task with the new title and status only if provided
       if ($request->has('title')) {
           $task->title = $request->input('title');
       }
       
       $task->is_complete = $request->input('status') === 'complete';
       $task->save();
       
       return response()->json($task);
   }
   

   // Remove the specified resource from storage
   public function destroy( Request $request,$id)
   {
    $task=Task::find($id);
       $task->delete();
       return response()->json(['success' => true]);
   }
}
