<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the brands.
     */
    public function index()
    {
        // Retrieve all brands from the database
        $brands = Brand::all();
        return view('brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        // Validate the request input
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name', // Unique brand name
            'description' => 'nullable|string|max:1000',
        ]);

        // Create the brand
        Brand::create($request->all());

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand)
    {
        // Show details of a specific brand
        return view('brands.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        // Validate the request input
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string|max:1000',
        ]);

        // Update the brand
        $brand->update($request->all());

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(Brand $brand)
    {
        // Delete the brand
        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
}
