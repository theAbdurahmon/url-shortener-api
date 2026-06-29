<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    protected function currentAuthUser(): User
    {
        return auth()->user();
    }
}
