<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    //

    public function index()
    {
        return view('sample.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sample.form');
    }


    public function getAll(){

        $samples = Sample::latest()->get();

        return response()->json([
            'samples' => $samples
        ], 200);
    }


    public function store(Request $request){


        $this->validate($request, [
            'code' => 'required|string',
            'description' => 'string',
        ]);

        $sample = new sample();

        $sample->code = $request->code;
        $sample->description = $request->description;

        $sample->save();

        return response()->json("Sample Created", 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required|string',
            'description' => 'string',
        ]);

        $sample = sample::findOrFail($id);

        $sample->code = $request->code;
        $sample->description = $request->description;

        $sample->save();

        return response()->json('ok',200);

    }

    public function destroy($id)
    {
        $sample = Sample::findOrFail($id);

        $sample->delete();

        return response()->json('ok', 200);
    }

    public function search(Request $request){
        $searchWord = $request->get('s');
        $samples = Sample::where(function($query) use ($searchWord){
            $query->where('code', 'LIKE', "%$searchWord%")
            ->orWhere('description', 'LIKE', "%$searchWord%");
        })->latest()->get();
        return response()->json([
            'types' => $samples
        ], 200);

    }
}
