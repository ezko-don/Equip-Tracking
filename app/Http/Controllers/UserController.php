<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        
        return User::where('name', 'like', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->take(10)
            ->get(['id', 'name']);
    }
} 