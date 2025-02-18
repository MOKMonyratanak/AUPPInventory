<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CompanyController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Unauthorized access.');
        }

        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Unauthorized access.');
        }

        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        Company::create($request->validated());
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Unauthorized access.');
        }

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Unauthorized access.');
        }

        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());
        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('companies.index')->with('error', 'Unauthorized access. Only admins can delete companies.');
        }
    
        try {
            $company->delete();
            return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Foreign key constraint violation
                return redirect()->route('companies.index')->with('error', 'Cannot delete this company because it is linked to existing users or assets.');
            }
    
            return redirect()->route('companies.index')->with('error', 'An error occurred while deleting the company.');
        }
    }
}
