<?php

namespace App\Http\Controllers;

// import model product
use App\Models\Product;
use Illuminate\Contracts\Cache\Store;
// import return type view
use illuminate\View\View;

// import facedes storage
use Illuminate\Support\Facades\Storage;


// import return type RedirectResponse
use illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Get all product
        $products = Product::latest()->paginate(10);

        // render view with product
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        //
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
        // validate form 
        $request->validate([
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        // upload image
        $image = $request->file('image');
        // $image->storeAs('public/products', $image->hashName());
        // upload image ke disk 'public'
        $image->storeAs('products', $image->hashName(), 'public');


        // create product
        Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'description'   => $request->description,
            'price'         => $request->price,
            'stock'         => $request->stock
        ]);

        // redirect ke index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasi Disimpan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        //
        // get product by id
        $product = Product::findOrFail($id);

        // render view with products
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // get product by id
        $product = Product::findOrFail($id);

        // render view with products
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        //validate form
        $request->validate([
            'image'         => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // Ganti 'required' dengan 'nullable'
            'title'         => 'required|min:5',
            'description'   => 'required|min:10',
            'price'         => 'required|numeric',
            'stock'         => 'required|numeric'
        ]);

        // get product by id.
        $product = Product::findOrFail($id);

        // check if image is uploaded
        if ($request->hasFile('image')) {

            // upload new image
            $image = $request->file('image');
            // $image->storeAs('public/products', $image->hashName());
            // upload image ke disk 'public'
            $image->storeAs('products', $image->hashName(), 'public');

            // delete old image
            // Storage::delete('products' . $product->image, 'public');
            Storage::disk('public')->delete('products/' . $product->image);


            // update product with new image
            $product->update([
                'image'         => $image->hashName(),
                'title'         => $request->title,
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock
            ]);
        } else {

            // update product without image
            $product->update([
                'title'         => $request->title,
                'description'   => $request->description,
                'price'         => $request->price,
                'stock'         => $request->stock
            ]);
        }

        // redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasi Diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //
        // get product by id.
        $product = Product::findOrFail($id);

        //  delete image
        // Storage::delete('products'.$product->image, 'public');
        // Storage::delete('products/' . $product->image);
        Storage::disk('public')->delete('products/' . $product->image);



        // delete product
        $product->delete();

        // redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasi Dihapus']);
    }
}
