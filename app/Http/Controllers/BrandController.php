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
        $brands = Brand::all();
        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        Brand::create($request->validated());
        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function show(Brand $brand)
    {
        return view('brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());
        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        // Check if the authenticated user is an admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('brands.index')->with('error', 'Unauthorized access. Only admins can delete brands.');
        }
    
        try {
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
