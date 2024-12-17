<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // List all companies
    public function index()
    {
        $companies = Company::all();
        return view('companies.index', compact('companies'));
    }

    // Show the form for creating a new company
    public function create()
    {
        return view('companies.create');
    }

    // Store a newly created company in the database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Company::create($validatedData);

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    // Show a specific company
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    // Show the form for editing an existing company
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    // Update a specific company in the database
    public function update(Request $request, Company $company)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $company->update($validatedData);

        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    // Delete a specific company
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
