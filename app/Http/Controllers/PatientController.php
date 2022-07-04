<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
// use App\Helpers\Helper;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('patient.index');
    }

    public function generateUniqueNumber()
    {
        do {
            $batch = random_int(100000, 999999);
        } while (Patient::where("customerId", "=", $batch)->first());

        return $batch;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('patient.form');
    }

    public function getAll(){

        $patients = Patient::latest()->get();

        return response()->json([
            'patients' => $patients
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $patientUniqueID = Helper::IDGenerator(new Patient, 'id', 3, 'GTI'); /** Generate id */
        $patientUniqueID = 'GTI-'.$this->generateUniqueNumber();

        $this->validate($request, [
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'gender' => 'required|string',
            'alt_mobile' => 'required|string',
            'digital_address' => 'required|string',
            'id_type' => 'required|string',
            'id_number' => 'required|string|max:25',
            'mobile' => 'required|string|max:10',
            'email' => 'required|email|unique:patients'
        ]);

        $patient = new Patient();

        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->middle_name = $request->middle_name;
        $patient->email = $request->email;
        $patient->mobile = $request->mobile;
        $patient->gender = $request->gender;
        $patient->alt_mobile = $request->alt_mobile;
        $patient->digital_address = $request->digital_address;
        $patient->id_type = $request->id_type;
        $patient->id_number = $request->id_number;
        $patient->address = $request->address;
        $patient->customerId = $patientUniqueID;

        $patient->save();

        return response()->json("Patient Created", 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'gender' => 'required|string',
            'alt_mobile' => 'required|string',
            'digital_address' => 'required|string',
            'id_type' => 'required|string',
            'id_number' => 'required|string|max:25',
            'mobile' => 'required|string|max:10',
            'email' => 'required|email|unique:patients,email,'.$id
        ]);

        $patient = Patient::findOrFail($id);

        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->middle_name = $request->middle_name;
        $patient->email = $request->email;
        $patient->mobile = $request->mobile;
        $patient->gender = $request->gender;
        $patient->alt_mobile = $request->alt_mobile;
        $patient->digital_address = $request->digital_address;
        $patient->id_type = $request->id_type;
        $patient->id_number = $request->id_number;
        $patient->address = $request->address;

        $patient->save();

        return response()->json('ok',200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        $patient->delete();

        return response()->json('ok', 200);
    }


    public function search(Request $request){
        $searchWord = $request->get('s');
        $patients = Patient::where(function($query) use ($searchWord){
            $query->where('first_name', 'LIKE', "%$searchWord%")
            ->orWhere('last_name', 'LIKE', "%$searchWord%")
            ->orWhere('middle_name', 'LIKE', "%$searchWord%")
            ->orWhere('mobile', 'LIKE', "%$searchWord%")
            ->orWhere('email', 'LIKE', "%$searchWord%");
        })->latest()->get();
        return response()->json([
            'patients' => $patients
        ], 200);

    }
}
