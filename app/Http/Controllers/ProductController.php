<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //Get All
    public function index()
    {
        $productColl = Product::all();

        return response()->json($productColl, 200);
    }

    //Add new Product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productName' => ['required', 'string', 'min:3'],
            'productDescription' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'categoryName' => ['required', 'exists:categories,categoryName']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $categoryID = Category::all()
                ->where('categoryName', '=', $request['categoryName'])
                ->pluck('id')->pop();

            Product::create(
                [
                    'productName' => $request['productName'],
                    'productDescription' => $request['productDescription'],
                    'price' => $request['price'],
                    'category_id' => $categoryID
                ]);
            return response()->json(["message" => "Product created"]);
        }
    }

    //Show specific product
    public function show($id)
    {
        $product = Product::findorfail($id);

        return response()->json($product);
    }

    //Update existing product
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'productName' => ['required', 'string', 'min:2'],
            'productDescription' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'categoryName' => ['required', 'exists:categories,categoryName']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $categoryID = Category::all()
                ->where('categoryName', '=', $request['categoryName'])
                ->pluck('id')->pop();

            Product::where('id', $id)->update(
                [
                    'productName' => $request['productName'],
                    'productDescription' => $request['productDescription'],
                    'price' => $request['price'],
                    'category_id' => $categoryID
                ]);

            return response()->json(['message' => 'Product updated']);
        }
    }

    //Delete product
    public function destroy($id)
    {
        Product::findorfail($id)->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
