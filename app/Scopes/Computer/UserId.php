<?php

namespace App\Scopes\Computer;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class UserId implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('user_id', Auth::id());
    }
}