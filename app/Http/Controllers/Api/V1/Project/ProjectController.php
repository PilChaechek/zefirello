<?php

namespace App\Http\Controllers\Api\V1\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Project\StoreProjectRequest;
use App\Http\Requests\Api\V1\Project\UpdateProjectRequest;
use App\Http\Resources\Api\V1\Project\ProjectResource;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Project::class);

        $user = $request->user();

        if ($user->hasRole('admin')) {
            $projects = Project::with('users')->latest()->get();
        } else {
            $projects = $user->projects()->with('users')->latest()->get();
        }

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): ProjectResource
    {
        $this->authorize('create', Project::class);

        $validated = $request->validated();
        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'slug' => $slug,
        ]);

        // Прикрепляем всех пользователей из запроса
        if (! empty($validated['users'])) {
            $project->users()->attach($validated['users']);
        } else {
            // Если массив пуст, прикрепляем только создателя
            $project->users()->attach($request->user()->id);
        }

        $project->load('users');

        return new ProjectResource($project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        $project->load('users');

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        // 1. Обновляем поля самого проекта, исключая ключ 'users'
        $projectData = Arr::except($validated, ['users']);
        $project->update($projectData);

        // 2. Отдельно и явно синхронизируем пользователей
        if (array_key_exists('users', $validated)) {
            $project->users()->sync($validated['users'] ?? []);
        }

        $project->load('users');

        return new ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }
}
