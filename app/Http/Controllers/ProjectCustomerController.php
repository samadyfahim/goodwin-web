<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Http\Request;

/**
 * Controller for managing project customers.
 * Handles adding and removing customers from projects.
 */
class ProjectCustomerController extends Controller
{
    /**
     * Add one or more customers to a project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'customer_ids' => 'sometimes|array',
            'customer_ids.*' => 'exists:customers,id',
            'customer_id' => 'sometimes|exists:customers,id', // For backward compatibility
        ]);

        $customerIds = [];
        
        // Handle both single customer_id and array of customer_ids
        if ($request->has('customer_ids') && is_array($request->customer_ids)) {
            $customerIds = $request->customer_ids;
        } elseif ($request->has('customer_id')) {
            $customerIds = [$request->customer_id];
        }

        if (empty($customerIds)) {
            return back()->withErrors(['customer_ids' => 'No customers selected.']);
        }

        $project->customers()->syncWithoutDetaching($customerIds);
        
        $count = count($customerIds);
        $message = $count === 1 ? 'Customer added!' : "{$count} customers added!";
        
        return back()->with('success', $message);
    }

    /**
     * Remove a customer from a project.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project, Customer $customer)
    {
        $project->customers()->detach($customer->id);
        return back()->with('success', 'Customer removed!');
    }
} 