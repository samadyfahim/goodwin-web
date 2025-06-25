<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

/**
 * Controller for providing customer suggestions for autocomplete/search.
 */
class CustomerSuggestionController extends Controller
{
    /**
     * Return a list of customer suggestions based on a search query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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