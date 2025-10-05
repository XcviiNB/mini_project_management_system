<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');

        $projects = Project::with(['owner'])
            ->withCount(['tasks as total_tasks' => function ($query) {
                $query->withTrashed(false);
            }])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where('start_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('end_date', '<=', $endDate);
            })
            ->paginate(10);

        $projectStats = DB::table('projects')
            ->join('users', 'projects.user_id', '=', 'users.id')
            ->leftJoin('tasks', function ($join) {
                $join->on('projects.id', '=', 'tasks.project_id')
                    ->whereNull('tasks.deleted_at');
            })
            ->select(
                'projects.id',
                'projects.name',
                'users.name as owner_name',
                DB::raw('COUNT(CASE WHEN tasks.status = "completed" THEN 1 END) as completed_tasks'),
                DB::raw('COUNT(CASE WHEN tasks.status = "pending" THEN 1 END) as pending_tasks')
            )
            ->groupBy('projects.id', 'projects.name', 'users.name')
            ->when($search, function ($query, $search) {
                return $query->where('projects.name', 'like', "%{$search}%");
            })
            ->when($startDate, function ($query, $startDate) {
                return $query->where('projects.start_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('projects.end_date', '<=', $endDate);
            })
            ->get();

        return Inertia::render('Projects/Index', [
            'projects'      => $projects,
            'projectStats'  => $projectStats,
            'filters'       => $request->only('search', 'start_date', 'end_date'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Project/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = auth()->user()->projects()->create($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        if ($project->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only view projects you own or as admin.');
        }

        $project->load(['owner', 'tasks' => function ($query) {
            $query->withTrashed(false)
                ->with(['user', 'comments.user'])
                ->orderBy('due_date', 'asc');
        }]);

        return Inertia::render('Projects/Show', ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        if ($project->user_id !== auth()->id() && !in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized: You can only edit your own projects.');
        }

        return Inertia::render('Projects/Edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)->with('success', 'Project updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if ($project->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized: You can only delete your own projects');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
