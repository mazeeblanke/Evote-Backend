<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can verify another user.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function verify(User $user)
    {
        // return true;
        dd($user->hasRole('admin'));
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update role.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function performAdminRole(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        //
    }
}
