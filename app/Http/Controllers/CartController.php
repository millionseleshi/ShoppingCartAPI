<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cartColl = Cart::all();
        foreach ($cartColl as $cart) {
            $user = User::find($cart->user_id);
            $response = [
                'cartID' => $cart->id,
                'userID' => $user->id,
                'userName' => User::where('id', $cart->user_id)
                    ->pluck('userName')->pop(),
                'productName' => Product::where('id', $cart->product_id)
                    ->pluck('productName')->pop(),
                'itemPrice' => $user->product()->pluck("price")->sum()
            ];

            $responseColl[] = $response;
            $uniqueCart = collect($responseColl)->unique('userID')->values()->all();
        }
        return response()->json($uniqueCart, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = [
            'userId.exists' => 'Invalid user',
            'productId.exists' => 'Invalid product'
        ];
        $validator = Validator::make($request->all(), [
            'userId' => ['required', 'exists:users,id'],
            'productId' => ['required', 'exists:products,id']
        ], $message);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            Cart::create(
                [
                    'user_id' => $request['userId'],
                    'product_id' => $request['productId']
                ]);

            return response()->json(["message" => 'Product added to cart']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = Cart::findorfail($id);

        $productName = Product::all()->where('id', $cart->product_id)->pluck('productName')->pop();
        $userName = User::all()->where('id', $cart->user_id)->pluck('userName')->pop();

        return response()->json(
            [
                'id' => $cart->id,
                'productName' => $productName,
                'userName' => $userName
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = [
            'userId.exists' => 'Invalid user',
            'productId.exists' => 'Invalid product'
        ];
        $validator = Validator::make($request->all(), [
            'userId' => ['required', 'exists:users,id'],
            'productId' => ['required', 'exists:products,id']
        ], $message);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $updated = Cart::where('id', $id)->update([
                'user_id' => $request['userId'],
                'product_id' => $request['productId']
            ]);

            if ($updated == true) {
                return response()->json(['message' => 'Cart Updated']);
            } else {
                return response()->json(['message' => 'Cart not found']);
            }


        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = Cart::destroy($id);
        if ($deleted == true) {
            return response()->json(['message' => 'Cart deleted']);
        } else {
            return response()->json(['message' => 'Cart not found']);
        }
    }

    public function removeProduct(Request $request)
    {
        $message = [
            'userId.exists' => 'Invalid user',
            'productId.exists' => 'Invalid product'
        ];
        $validator = Validator::make($request->all(), [
            'userId' => ['required', 'exists:users,id'],
            'productId' => ['required', 'exists:products,id']
        ], $message);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $removed = Cart::where('user_id', $request->userId)
                ->where('product_id', $request->productId)
                ->delete();

            if ($removed == true) {
                return response()->json(['message' => "Product removed form cart"]);
            } else {
                return response()->json(['message' => "User or Product not found"]);
            }
        }


    }
}
