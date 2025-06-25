<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

/**
 * Controller for providing user suggestions for autocomplete/search.
 */
class UserSuggestionController extends Controller
{
    /**
     * Return a list of user suggestions based on a search query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $q = $request->input('q');
        $users = User::query()
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%") ;
                });
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
} 