<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use Laravel\Cashier\SubscriptionItem;

class SubscriptionItemPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Cashier\SubscriptionItem $subscriptionItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SubscriptionItem $subscriptionItem)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Cashier\SubscriptionItem  $subscriptionItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SubscriptionItem $subscriptionItem)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Cashier\SubscriptionItem  $subscriptionItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SubscriptionItem $subscriptionItem)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Cashier\SubscriptionItem  $subscriptionItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SubscriptionItem $subscriptionItem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \Laravel\Cashier\SubscriptionItem  $subscriptionItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SubscriptionItem $subscriptionItem)
    {
        //
    }
}
