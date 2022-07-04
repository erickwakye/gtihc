<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        return view('price.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('price.form');
    }


    public function getAll(){

        $prices = Price::latest()->get();

        return response()->json([
            'prices' => $prices
        ], 200);
    }


    public function store(Request $request){


        $this->validate($request, [
            'test' => 'required|string',
            'amount' => 'required|string',
            'tax' => 'string',
            'vat' => 'string',
            'levy' => 'string',
        ]);

        $price = new Price();

        $price->test = $request->test;
        $price->amount = $request->amount;
        $price->vat = $request->vat;
        $price->levy = $request->levy;
        $price->tax = $request->tax;

        $price->save();

        return response()->json("Price Created", 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'test' => 'required|string',
            'amount' => 'required|string',
            'vat' => 'string',
            'tax' => 'string',
            'levy' => 'string',
        ]);

        $price = Price::findOrFail($id);

        $price->test = $request->test;
        $price->amount = $request->amount;
        $price->vat = $request->vat;
        $price->tax = $request->tax;
        $price->levy = $request->levy;

        $price->save();

        return response()->json('ok',200);
    }

    public function destroy($id)
    {
        $price = Price::findOrFail($id);

        $price->delete();

        return response()->json('ok', 200);
    }

    public function search(Request $request){
        $searchWord = $request->get('s');
        $prices = Price::where(function($query) use ($searchWord){
            $query->where('test', 'LIKE', "%$searchWord%")
            ->orWhere('amount', 'LIKE', "%$searchWord%");
        })->latest()->get();
        return response()->json([
            'prices' => $prices
        ], 200);

    }
}
