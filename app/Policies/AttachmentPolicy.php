<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attachment $attachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Авторизация для создания вложений делегируется политике родительской сущности (например, TaskPolicy).
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attachment $attachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attachment $attachment): Response
    {
        // Проверить, является ли пользователь владельцем вложения
        if ($user->id === $attachment->user_id) {
            return Response::allow();
        }

        // Проверить, является ли пользователь участником проекта, к которому относится задача
        // Получаем Task через полиморфное отношение и затем его Project
        $attachable = $attachment->attachable;

        if ($attachable instanceof Task) {
            $project = $attachable->project;
            if ($project && $project->users->contains($user)) {
                return Response::allow();
            }
        }

        return Response::deny('You do not own this attachment or are not a member of the project.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attachment $attachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attachment $attachment): bool
    {
        return false;
    }
}
