<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index() {

        $tasks = Task::all();

        return response()->json([
            'tasks' => $tasks
        ], 201);

    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => auth()->id()
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('task-images', 'public');
                $task->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task->load('images')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);

        return response()->json([
            'task' => $task
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'exists:task_images,id'
        ]);

        $task->update([
            'title' => $request->title ?? $task->title,
            'description' => $request->description ?? $task->description,
            'status' => $request->status ?? $task->status,
            'due_date' => $request->due_date ?? $task->due_date
        ]);

        // Handle image removals if specified
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imageId) {
                $image = $task->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('task-images', 'public');
                $task->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task->load('images')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);

        // Delete associated images from storage
        foreach ($task->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete the task (this will also delete associated images from database due to cascade)
        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ], 200);
    }
}
