<?php

namespace App\Models\Traits;

use App\Models\Scopes\OwnerScope;

trait OwnerScopeTrait
{
    /**
     * bootOwnerScopeTrait: boot the OwnerScope in the model directly as a global scope.
     *
     * @return void
     */
    public static function bootOwnerScopeTrait()
    {
        static::addGlobalScope(new OwnerScope);
    }
}
