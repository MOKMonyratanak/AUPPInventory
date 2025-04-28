<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class BrandController extends Controller
{
    public function index()
    {
        try {
            $brands = Brand::all();
            return view('brands.index', compact('brands'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('brands.create');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function store(StoreBrandRequest $request)
    {
        try {
            Brand::create($request->validated());
            return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Brand $brand)
    {
        try {
            return view('brands.show', compact('brand'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(Brand $brand)
    {
        try {
            return view('brands.edit', compact('brand'));
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        try {
            $brand->update($request->validated());
            return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            // Check if the authenticated user is an admin
            if (auth()->user()->role !== 'admin') {
                return redirect()->route('brands.index')->with('error', 'Unauthorized access. Only admins can delete brands.');
            }

            $brand->delete();
            return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Foreign key constraint violation
                return redirect()->route('brands.index')->with('error', 'Cannot delete this brand because it is linked to existing assets.');
            }
    
            return redirect()->route('brands.index')->with('error', 'An error occurred while deleting the brand.');
        }
    }
}
