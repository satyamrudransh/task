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
       // Retrieve paginated tasks (10 per page). You can change to Task::all() if pagination isn't needed.
       $tasks = Task::paginate(10);

       // Return tasks in JSON format with a flag indicating if there are more pages to load.
       return response()->json([
           'tasks' => $tasks->items(), // Extract the task items for the current page
           'hasMore' => $tasks->hasMorePages() // Indicates if there are more pages to load
       ]);
   }

   // Store a newly created resource in storage
   public function store(Request $request)
   {
       // Validate the request data. Title is required, must be a string, max 255 characters, and unique in the 'task' table.
       $request->validate([
           'title' => 'required|string|max:255|unique:task,title',
       ]);

       // Create a new task with the validated title and set is_complete to false.
       $task = Task::create([
           'title' => $request->input('title'),
           'is_complete' => false,
       ]);

       // Return the newly created task in JSON format.
       return response()->json($task);
   }

   // Mark the specified resource as complete or update its title
   public function update(Request $request, $id)
   {
       // Find the task by its ID or fail if not found.
       $task = Task::findOrFail($id);
       
       // Validate the request data. Status must be either 'pending' or 'complete', and title must be unique excluding the current task.
       $request->validate([
           'status' => 'in:pending,complete',
           'title' => [
               'string',
               'max:255',
               Rule::unique('task')->ignore($id) // Ensure title is unique except for the current task
           ],
       ]);
   
       // Update the task title if provided in the request
       if ($request->has('title')) {
           $task->title = $request->input('title');
       }
       
       // Update the completion status based on the provided status ('complete' or 'pending')
       $task->is_complete = $request->input('status') === 'complete';
       $task->save(); // Save the updated task

       // Return the updated task in JSON format.
       return response()->json($task);
   }
   
   // Remove the specified resource from storage
   public function destroy(Request $request, $id)
   {
       // Find the task by its ID or fail if not found
       $task = Task::find($id);
       
       // Delete the task from the database
       $task->delete();
       
       // Return a success response in JSON format
       return response()->json(['success' => true]);
   }
}
