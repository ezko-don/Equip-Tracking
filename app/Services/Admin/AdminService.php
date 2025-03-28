<?php

namespace App\Services\Admin;

class AdminService
{
    public function __invoke()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function check()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
} 