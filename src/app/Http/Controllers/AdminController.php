<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use  Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view("admin.index");
    }

    # ADMIN - BRANDS Entity begin section
    public function brands()
    {
        $brandList = Brand::orderBy("id", "desc")->paginate(10);
        return view("admin.brands", compact("brandList"));
    }

    // Routing to admin add-brand page
    public function add_brand()
    {
        return view("admin.brand-add");
    }

    // SAVE Brands
    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);


        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . "." . $file_extension;
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;

        $brand->save();

        return redirect()->route('admin.brands')->with('status', '
            Brand has been added successfully!
        ');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;

        if ($request->hasFile('image')) {
            if (File::exists(public_path("uploads/brands") . '/' . $brand->image)) {
                File::delete(public_path("uploads/brands") . '/' . $brand->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . "." . $file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->save();

        return redirect()->route('admin.brands')->with('status', '
            Brand has been update successfully!
        ');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path("uploads/brands");
        $img = Image::read($image->path());

        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function brand_delete($id)
    {
        $brand = Brand::find($id);
        if (File::exists(public_path("uploads/brands") . '/' . $brand->image)) {
            File::delete(public_path('uploads/brands') . '/' . $brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand has been deleted successfully!');
    }
    # ADMIN - BRANDS Entity end section

    # ADMIN - CATEGORIES Entity begin section
    public function categories()
    {
        $list_categories = Category::orderBy('id', 'DESC')->paginate(10);
        return view('admin.categories', compact('list_categories'));
    }

    public function add_category()
    {
        return view("admin.category-add");
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);


        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . "." . $file_extension;
        $this->GenerateCategoryThumbnailsImage($image, $file_name);
        $category->image = $file_name;

        $category->save();

        return redirect()->route('admin.categories')->with('status', '
            Category has been added successfully!
        ');
    }

    public function GenerateCategoryThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path("uploads/categories");
        $img = Image::read($image->path());

        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . '/' . $imageName);
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories') . '/' . $request->image)) {
                File::delete(public_path('uploads/categories') . '/' . $request->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->getExtension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route('admin.categories')->with('status', '
            Category has been update successfully!
        ');
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if (File::exists(public_path('uploads/categories') . '/' . $category->image)) {
            File::delete(public_path('uploads/categories') . '/' . $category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted successfully!');
    }
    # ADMIN - CATEGORIES Entity end section

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
            foreach (explode(',', $product->image) as $oldFile) {
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

    # ADMIN - PRODUCTS Entity end section
}
