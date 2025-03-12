<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $o_Column = "";
        $o_order = "";
        $order = $request->query('order') ? $request->query('order') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        // filter by price
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 1000;

        switch ($order) {
            case 1:
                $o_Column = 'created_at';
                $o_order = 'DESC';
                break;

            case 2:
                $o_Column = 'created_at';
                $o_order = 'ASC';
                break;

            case 3:
                $o_Column = 'regular_price';
                $o_order = 'ASC';
                break;

            case 4:
                $o_Column = 'regular_price';
                $o_order = 'DESC';
                break;

            default:
                $o_Column = 'id';
                $o_order = 'DESC';
                break;
        }

        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();
        $listProducts = Product::where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "'=''");
        })
            ->where(function ($query) use ($f_categories) {
                $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "'=''");
            })
            ->where(function ($query) use ($min_price, $max_price) {
                $query->whereBetween('regular_price', [$min_price, $max_price])
                    ->orWhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_Column, $o_order)->paginate($size);
        return view('front.shop', compact('listProducts', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $productBySlug = Product::where('slug', $product_slug)->first();
        $rProducts = Product::where('slug', '<>', $product_slug)->get()->take(8);
        return view('front.details', compact('productBySlug', 'rProducts'));
    }
}
