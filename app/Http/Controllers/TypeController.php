<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    //

    public function index()
    {
        return view('type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('type.form');
    }


    public function getAll(){

        $types = Type::latest()->get();

        return response()->json([
            'types' => $types
        ], 200);
    }


    public function store(Request $request){


        $this->validate($request, [
            'code' => 'required|string',
            'description' => 'string',
        ]);

        $type = new Type();

        $type->code = $request->code;
        $type->description = $request->description;

        $type->save();

        return response()->json("Type Created", 200);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required|string',
            'description' => 'string',
        ]);

        $type = Type::findOrFail($id);

        $type->code = $request->code;
        $type->description = $request->description;

        $type->save();

        return response()->json('ok',200);

    }

    public function destroy($id)
    {
        $type = Type::findOrFail($id);

        $type->delete();

        return response()->json('ok', 200);
    }

    public function search(Request $request){
        $searchWord = $request->get('s');
        $types = Type::where(function($query) use ($searchWord){
            $query->where('code', 'LIKE', "%$searchWord%")
            ->orWhere('description', 'LIKE', "%$searchWord%");
        })->latest()->get();
        return response()->json([
            'types' => $types
        ], 200);

    }


}
