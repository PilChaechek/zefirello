<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Project $project): bool
    {
        // Пользователь может видеть список задач, если он является участником проекта
        return $project->users->contains($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project, Task $task): bool
    {
        // Пользователь может видеть задачу, если он является участником проекта, к которому относится задача
        return $project->users->contains($user) && $task->project_id === $project->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        // Пользователь может создавать задачи, только если он администратор
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Пользователь может обновлять задачу, если он является участником проекта
        return $task->project->users->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project, Task $task): bool
    {
        // Пользователь может удалять задачу, если он является участником проекта, и задача относится к этому проекту
        return $project->users->contains($user) && $task->project_id === $project->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project, Task $task): bool
    {
        return false;
    }
}
