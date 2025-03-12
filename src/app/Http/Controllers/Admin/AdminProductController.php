<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminProductController extends Controller
{
    # ADMIN - PRODUCTS Entity begin section
    public function products()
    {
        $list_products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('list_products'));
    }

    public function products_add()
    {
        $brandList = Brand::select('id', 'name')->orderBy('name')->get();
        $list_categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('admin.product_add', compact('list_categories', 'brandList'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'category_id' => 'required',
            'brand_id' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048'
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            $allowedFileExtensions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedFileExtensions);
                if ($gcheck) {
                    $gfilename = $current_timestamp . '-' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfilename);
                    array_push($gallery_arr, $gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnalis');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());

        $img->cover(540, 689, 'top');

        $img->resize(540,  689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);

        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail . '/' . $imageName);
    }

    public function product_edit($id)
    {
        // $product = DB::select("SELECT * FROM product WHERE id = ?", [$id]);
        $product = Product::find($id);
        $list_categories = DB::select('select id, name from categories order by name');
        $brandList = DB::select('select id, name from brands order by name');
        return view('admin.product-edit', compact('product', 'list_categories', 'brandList'));
    }

    public function product_update(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $request->id,
            'category_id' => 'required',
            'brand_id' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        // $product = new Product;

        // $data = DB::query("select * from product where id = ?", [$request->id]);
        // foreach ($data as $item) {
        //     $product->name = $item->name;
        //     $product->slug = Str::slug($item->name);
        //     $product->short_description = $item->short_description;
        //     $product->description = $item->description;
        //     $product->regular_price = $item->regular_price;
        //     $product->sale_price = $item->sale_price;
        //     $product->SKU = $item->SKU;
        //     $product->stock_status = $item->stock_status;
        //     $product->featured = $item->featured;
        //     $product->quantity = $item->quantity;
        //     $product->category_id = $item->category_id;
        //     $product->brand_id = $item->brand_id;
        //     $product->image = $item->image;
        //     $product->images = $item->images;
        // }
        // $product->name = $request->name;
        // $product->slug = Str::slug($request->name);
        // $product->short_description = $request->short_description;
        // $product->description = $request->description;
        // $product->regular_price = $request->regular_price;
        // $product->sale_price = $request->sale_price;
        // $product->SKU = $request->SKU;
        // $product->stock_status = $request->stock_status;
        // $product->featured = $request->featured;
        // $product->quantity = $request->quantity;
        // $product->category_id = $request->category_id;
        // $product->brand_id = $request->brand_id;

        // get current timestamp in php laravel
        $current_timestamp = Carbon::now()->timestamp;

        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/products')) . '/' . $product->image) {
                File::delete(public_path('uploads/products') . '/' . $product->image);
            }
            if (File::exists(public_path('uploads/products/thumbnalis')) . '/' . $product->image) {
                File::delete(public_path('uploads/products/thumbnalis') . '/' . $product->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->getExtension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateProductThumbnailImage($image, $file_name);
            $product->image = $file_name;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if ($request->hasFile('images')) {
            foreach (explode(',', $product->images) as $oldFile) {
                if (File::exists(public_path('uploads/products')) . '/' . $oldFile) {
                    File::delete(public_path('uploads/products') . '/' . $oldFile);
                }
                if (File::exists(public_path('uploads/products/thumbnalis')) . '/' . $oldFile) {
                    File::delete(public_path('uploads/products/thumbnalis') . '/' . $oldFile);
                }
            }

            $allowedFileExtensions = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedFileExtensions);
                if ($gcheck) {
                    $gfilename = $current_timestamp . '-' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfilename);
                    array_push($gallery_arr, $gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $product->images = $gallery_images;
        }

        $product->save();
        return redirect()->route('admin.products')->with('status', '
            Product has been update successfully!
        ');
    }

    public function product_delete($product_id)
    {
        // $product = DB::select("SELECT * FROM product WHERE id = ?", [$product_id]);
        $product = Product::find($product_id);
        if (File::exists(public_path('uploads/products') . "/" . $product->image)) {
            File::delete(public_path('uploads/products') . "/" . $product->image);
        }
        if (File::exists(public_path('uploads/products/thumbnalis')) . '/' . $product->image) {
            File::delete(public_path('uploads/products/thumbnalis') . '/' . $product->image);
        }

        foreach (explode(",", $product->images) as $oFile) {
            if (File::exists(public_path('uploads/products') . '/' . $oFile)) {
                File::delete(public_path('uploads/products') . '/' . $oFile);
            }
            if (File::exists(public_path('uploads/products/thumbnalis') . '/' . $oFile)) {
                File::delete(public_path('uploads/products/thumbnalis') . '/' . $oFile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully!');
    }

    # ADMIN - PRODUCTS Entity end section
}
