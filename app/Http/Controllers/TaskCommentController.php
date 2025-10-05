<?php

namespace App\Http\Controllers;

use App\Models\TaskComment;
use App\Http\Requests\StoreTaskCommentRequest;
use App\Http\Requests\UpdateTaskCommentRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreTaskCommentRequest $request, Task $task)
    {
        $commentData = $request->validated();
        $commentData['user_id'] = auth()->id();

        $task->comments()->create($commentData);

        return redirect()->back()->with('success', 'Comment published');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task, TaskComment $taskComment)
    {
        if ($taskComment->user_id !== auth()->id() && $task->assigned_to !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only view your comments or assigned tasks.');
        }

        $taskComment->load(['user', 'task.project']);
        return Inertia::render('Tasks/Comments/Show', ['comment' => $taskComment, 'task' => $task]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task, TaskComment $taskComment)
    {
        if ($taskComment->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only edit your own comments.');
        }

        return Inertia::render('Tasks/Comments/Edit', ['comment' => $taskComment, 'task' => $task]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskCommentRequest $request, TaskComment $taskComment)
    {
        $taskComment->update($request->validated());

        return redirect()->back()->with('success', 'Comment edited.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task, TaskComment $taskComment)
    {
        if ($taskComment->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only delete your own comments.');
        }

        $taskComment->delete();

        return redirect()->back()->with('success', 'Comment removed.');
    }
}
