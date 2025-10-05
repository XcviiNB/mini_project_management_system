<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['project', 'assignee'])
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        return Inertia::render('Tasks/Index', ['tasks'  => $tasks]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project)
    {
        $users = User::whereIn('role', ['admin','manager', 'developer'])->get(['id', 'name']);

        return Inertia::render('Tasks/Create', ['project'   => $project, 'users'    => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $validated = $request->validated();
        $validated['status'] = $validated['status'] ?? 'pending';

        $project->tasks()->create($validated);

        return redirect()->route('projects.show', $project)->with('success', 'Task published');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task) {
        $task->update($request->validated());

        return redirect()->back()->with('success', 'Task status updated.');
    }

    public function tasksByUser(User $user) {
        $tasks = DB::table('tasks')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->join('users as assignees', 'tasks.assigned_to', '=', 'assignees.id')
            ->leftJoin('users as owners', 'projects.user_id', '=', 'owners.id')
            ->where('tasks.assigned_to', $user->id)
            ->whereNull('tasks.deleted_at')
            ->select(
                'tasks.id',
                'tasks.title',
                'tasks.status',
                'tasks.due_date',
                'projects.name as project_name',
                'owners.name as project_owner_name',
                'assignees.name as assignee_name'
            )
            ->orderBy('tasks.due_date', 'asc')
            ->get();

        return Inertia::render('Users/Tasks', ['user' => $user, 'tasks' => $tasks]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->assigned_to !== auth()->id() && $task->project->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only view assigned or owned tasks.');
        }
        $task->load(['project', 'assignee', 'comments.user']);

        return Inertia::render('Tasks/Show', ['task' => $task]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        if ($task->assigned_to !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized: You can only edit your assigned tasks.');
        }

        $users = User::whereIn('role', ['admin', 'manager', 'developer'])->get(['id', 'name']);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()->route('projects.show', $task->project)->with('success', 'Task updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->project->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only delete tasks in your projects.');
        }

        $task->delete();

        return redirect()->route('projects.show', $task->project)
            ->with('success', 'Task deleted successfully.');
    }
}
