<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return Product::all();
         // Initialize query
         $query = Product::query();

         // Filtering by price (e.g., products with price greater than min_price and less than max_price)
         if ($request->has('min_price')) {
             $query->where('price', '>=', $request->min_price);
         }
 
         if ($request->has('max_price')) {
             $query->where('price', '<=', $request->max_price);
         }
 
         // Sorting by name, price, or created_at
         if ($request->has('sort_by')) {
             $allowedSorts = ['name', 'price', 'created_at']; // Allowed fields to sort by
             $sortBy = $request->get('sort_by');
 
             if (in_array($sortBy, $allowedSorts)) {
                 // Get the sort order from query params, default to 'asc'
                 $sortOrder = $request->get('sort_order', 'asc');
                 // Ensure the sort order is either 'asc' or 'desc', otherwise default to 'asc'
                 $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'asc';
 
                 // Apply sorting to the query
                 $query->orderBy($sortBy, $sortOrder);
             }
         }
 
         // Return the result of the query
         return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'price' => 'required'
        ]);

        return Product::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Product::destroy($id);
    }

     /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        return Product::where('name', 'like', '%'.$name.'%')->get();
    }
}
