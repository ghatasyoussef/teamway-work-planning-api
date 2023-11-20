<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model:
     * only if the user is an admin or is showing self data.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        return ($user->id == $model->id) || ($user->is_admin);
    }

    /**
     * Determine whether the user can make another user an admin:
     * only if the user is an admin.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function makeAdmin(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update a user:
     * only if the user is an admin or is updating self data.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        return ($user->id == $model->id) || ($user->is_admin);
    }

    /**
     * Determine whether the user can delete a user:
     * only if the user is an admin.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        return $user->is_admin;
    }
}
