<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $customers = Customer::query()
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%$q%")
                        ->orWhere('company_name', 'like', "%$q%")
                        ->orWhere('email', 'like', "%$q%") ;
                });
            })
            ->limit(10)
            ->get(['id', 'name', 'company_name', 'email']);

        return response()->json($customers);
    }
} 