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
        try {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('users.index')->with('error', 'Unauthorized access.');
            }
    
            $companies = Company::all();
            return view('companies.index', compact('companies'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('users.index')->with('error', 'Unauthorized access.');
            }
    
            return view('companies.create');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function store(StoreCompanyRequest $request)
    {
        try {
            Company::create($request->validated());
            return redirect()->route('companies.index')->with('success', 'Company created successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Company $company)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('users.index')->with('error', 'Unauthorized access.');
            }
    
            return view('companies.show', compact('company'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('users.index')->with('error', 'Unauthorized access.');
            }
    
            return view('companies.edit', compact('company'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        try {
            $company->update($request->validated());
            return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage()); 
        }
    }

    public function destroy(Company $company)
    {
        try {
            // Check if the authenticated user is an admin
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('companies.index')->with('error', 'Unauthorized access. Only admins can delete companies.');
            }
            
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
