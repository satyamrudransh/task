<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

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
           'title' => 'required|max:255',
       ]);

       $task = Task::create([
           'title' => $request->title,
           'is_complete' => false,
       ]);

       return response()->json($task);
   }

   // Mark the specified resource as complete
   public function update(Task $task)
   {
       $task->update(['is_complete' => true]);
       return response()->json($task);
   }

   // Remove the specified resource from storage
   public function destroy(Task $task)
   {
       $task->delete();
       return response()->json(['success' => true]);
   }
}
