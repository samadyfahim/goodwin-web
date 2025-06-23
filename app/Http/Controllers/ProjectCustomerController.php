<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Customer;
use Illuminate\Http\Request;

class ProjectCustomerController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);
        $project->customers()->syncWithoutDetaching([$validated['customer_id']]);
        return back()->with('success', 'Customer added!');
    }

    public function destroy(Project $project, Customer $customer)
    {
        $project->customers()->detach($customer->id);
        return back()->with('success', 'Customer removed!');
    }
} 