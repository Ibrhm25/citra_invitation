<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // All Product
        $products = Product::all();

        // Return Json Response
        return response()->json([
            'product' => $products
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        try {
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
            // Create Product
            Product::create([
                'name' => $request->name,
                'image' => $imageName,
                'link' => $request->link,
                'description' => $request->description
            ]);

            // Save Image In Storage folder
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            //Return Json Response
            return response()->json([
                'message' => "Product Successfully Created"
            ], 200);
        } catch (\Exception $e) {
            // Return Json response
            return response()->json([
                'message' => 'Something went really wrong!'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Product detail
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product Not Found.'
            ], 404);
        }

        // Return Json Response
        return response()->json([
            'product' => $product
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductStoreRequest $request, $id)
    {
        try {
            //find product
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'message' => 'Product Not Found.'
                ], 404);
            }

            echo "request : $request->name";
            echo "description : $request->description";
            $product->name = $request->name;
            $product->description = $request->description;

            if ($request->image) {
                // Public storage
                $storage = Storage::disk('public');

                //old image delete
                if ($storage->exists($product->image))
                    $storage->delete($product->image);

                //image name
                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                $product->image = $imageName;

                //image save in public folder
                $storage->put($imageName, file_get_contents($request->image));
            }

            // update product
            $product->save();

            //return json response
            return response()->json([
                'message' => "Product Successfully Updated."
            ], 200);
        } catch (\Exception $e) {
            //return json response
            return response()->json([
                'message' => "Something Went Really Wrong!"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Detail
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Product Not Found.'
            ], 404);
        }

        //Public storage
        $storage = Storage::disk('public');

        // Image delete
        if ($storage->exists($product->image))
            $storage->delete($product->image);

        //Delete Product
        $product->delete();

        // Return json Response
        return response()->json([
            'message' => "Product Successfully Deleted"
        ], 200);
    }
}
