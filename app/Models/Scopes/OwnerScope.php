<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OwnerScope implements Scope
{
    /**
     * apply OwnerScope to the query.
     *  if the user is admin: get all relevant data.
     *  If the user is worker: Only get the user relevant data
     *
     * @param  mixed  $builder
     * @param  mixed  $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        if ($user->is_admin) {
            return;
        }

        $builder->whereHas('user', function ($q) use ($user) {
            $q->where('id', $user->id);
        });
    }
}
