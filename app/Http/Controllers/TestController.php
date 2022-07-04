<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Test;
use Illuminate\Http\Request;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{

    public function generateUniqueNumber()
    {
        do {
            $batch = random_int(100000, 999999);
        } while (Test::where("batch", "=", $batch)->first());

        return $batch;
    }

    public function payment()
    {
        return view('test.pay');
    }

    public function getPayments(){
        $per_page = 10;
        $searchWord = $_GET['s'] ?? null;
        $tests = Test::with(['patient', 'price'])
        ->whereNotNull('patient_id')
        ->when($searchWord,function($query,$searchWord){
            return $query->orWhereHas('patient',function ($query)use($searchWord)
           {
               $query->where('patients.customerId','Like','%'.$searchWord.'%')
                     ->where('patients.customerId','Like','%'.$searchWord.'%');
           });
     })
     ->orderBy('id', 'desc')->paginate($per_page);
        return response()->json([
            'tests' => $tests
        ], 200);
    }

    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function makePayment(Request $request){

        $batchUniqueID = $this->generateUniqueNumber();

        $this->validate($request, [
            'patient_id' => 'required',
            'price_id' => 'required',
            'payment_mode' => 'required|string',
            'doctor_ref' => 'string',
            'requested_test' => 'required|string',
        ]);

        $prices = $request->price_id;

        for($i=0; $i < count($prices);  $i++){
            $price = $prices[$i];
            $test = new Test();
            $test->patient_id = $request->patient_id;
            $test->price_id = $price;
            $test->payment_mode = $request->payment_mode;
            $test->doctors_ref = $request->doctor_ref;
            $test->requested_test = $request->requested_test;
            $test->payment_status = 1;
            $test->sample_id = 1;
            $test->type_id = 1;
            $test->tube_id = 1;
            $test->batch = $batchUniqueID;
            $test->save();
        }

        return response()->json("Payment Created", 200);
    }

    public function takesample(){
        return view('test.sample');
    }

    public function takemix(){
        return view('test.mixing');
    }

    public function getcontrolpage(){
        return view('test.control');
    }


    public function getresultpage(){
        return view('test.result');
    }
    public function getControlAvailable(){
        $per_page = 10;
        $searchWord = $_GET['s'] ?? null;
        $samples = Test::query()->with(['patient', 'price'])
        ->where("mixed_status", 1)
        ->where("payment_status", 1)
        ->where("controlled_status", 0)
        ->when($searchWord,function($query,$searchWord){
            return $query->orWhereHas('patient',function ($query)use($searchWord)
           {
               $query->where('patients.first_name', $searchWord)
               ->where("tests.mixed_status", 1)
               ->where("controlled_status", 0)
               ->where("tests.payment_status", 1);
           });
     })
        ->orderBy('id', 'desc')->paginate($per_page);
        return response()->json([
            'tests' => $samples
        ], 200);
    }

    public function getSamplesAvailable(){
        $per_page = 10;
        $searchWord = $_GET['s'] ?? null;
        $samples = Test::query()->with(['patient', 'price'])
        ->where("sample_status", 0)
        ->where("payment_status", 1)
        ->when($searchWord,function($query,$searchWord){
            return $query->orWhereHas('patient',function ($query)use($searchWord)
           {
               $query->where('patients.customerId','Like','%'.$searchWord.'%')
                     ->where("sample_status", 0)
                     ->where("payment_status", 1);
           });
     })
        ->orderBy('id', 'desc')->paginate($per_page);
        return response()->json([
            'tests' => $samples
        ], 200);
    }

    public function getMixesAvailable(){
        $per_page = 10;
        $searchWord = $_GET['s'] ?? null;
        $samples = Test::query()->with(['patient', 'price', 'sample'])
        ->where("sample_status", 1)
        ->where("payment_status", 1)
        ->where("controlled_status", 0)
        ->where("mixed_status", 0)
        ->when($searchWord,function($query,$searchWord){
               return $query->orWhereHas('patient',function ($query)use($searchWord)
              {
                  $query->where('patients.customerId','Like','%'.$searchWord.'%')
                        ->where("sample_status", 1)
                        ->where("payment_status", 1)
                        ->where("mixed_status", 0)
                        ->where("controlled_status", 0);
              });
        })
        ->orderBy('id', 'desc')->paginate($per_page);
        return response()->json([
            'tests' => $samples
        ], 200);
    }

    public function getResultsAvailable(){
        $per_page = 10;
        $searchWord = $_GET['s'] ?? null;
        $results = Test::query()->with(['patient', 'price', 'sample'])
        ->where("sample_status", 1)
        ->where("payment_status", 1)
        ->where("controlled_status", 1)
        ->when($searchWord,function($query,$searchWord){
               return $query->orWhereHas('patient',function ($query)use($searchWord)
              {
                  $query->where('patients.customerId','Like','%'.$searchWord.'%')
                        ->where("sample_status", 1)
                        ->where("payment_status", 1)
                        ->where("controlled_status", 1);
              });
        })
        ->orderBy('id', 'desc')->paginate($per_page);
        return response()->json([
            'tests' => $results
        ], 200);
    }

    public function getResultsForPay(Request $request){
        $id = $request->id;
        if($id){
            $data = Test::query()->with(['patient', 'price', 'sample'])
            ->where("payment_status", 1)
            ->where("batch", $id)
            ->distinct('batch')->first();
            $totals = Test::query()->with(['patient', 'price', 'sample'])
            ->where("payment_status", 1)
            ->where("batch", $id)->get();
            $products = DB::select("SELECT sum(prices.amount) as 'total' from tests inner join prices on tests.price_id = prices.id where batch = '$id' ");
            $results = Test::query()->with(['patient', 'price', 'sample'])
            ->where("payment_status", 1)
            ->where("batch", $id)
            ->orderBy('id', 'desc')->get();
            return response()->json([
                'tests' => $results,
                'datas' => $data,
                'totals' => $totals,
                'products' => $products
            ], 200);
        }else{
            return response()->json([
                'error' => 'Patient Not Found'
            ], 422);
        }
    }

    public function getResultsFromScan(Request $request){
        $id = $request->id;
        if($id){
            $data = Test::query()->with(['patient', 'price', 'sample'])
            // ->select('batch', 'id')
            ->where("sample_status", 1)
            ->where("payment_status", 1)
            ->where("controlled_status", 1)
            ->where("batch", $id)
            ->distinct('batch')->first();

            $results = Test::query()->with(['patient', 'price', 'sample'])
            ->where("sample_status", 1)
            ->where("payment_status", 1)
            ->where("controlled_status", 1)
            ->where("batch", $id)
            ->orderBy('id', 'desc')->get();

            return view('result.patientres', ['results' => $results, 'data' => $data]);
        }else{
            return response()->json([
                'error' => 'Patient Not Found'
            ], 422);
        }

    }

    public function getResultsFromMail(Request $request){

        $id = $request->id;

        if($id){
            $data = Test::query()->with(['patient', 'price', 'sample'])
            ->where("sample_status", 1)
            ->where("payment_status", 1)
            ->where("controlled_status", 1)
            ->where("batch", $id)
            ->distinct('batch')->first();

            $tests = Test::query()->with(['patient', 'price', 'sample'])
            ->where("sample_status", 1)
            ->where("payment_status", 1)
            ->where("controlled_status", 1)
            ->where("batch", $id)
            ->orderBy('id', 'desc')->get();

            return view('test.mailresult', ['tests' => $tests, 'data' => $data]);

        }else{

            return response()->json([
                'error' => 'Patient Not Found'
            ], 422);

        }

    }

    public function updateSampleTaken(Request $request, $id){
        $this->validate($request, [
            'sample_status' => 'required|integer',
            'sample_id' => 'required|integer'
        ]);
        $test = Test::findOrFail($id);
        $test->sample_status = $request->sample_status;
        $test->sample_id = $request->sample_id;
        $test->sample_by = auth()->user()->name;
        $test->save();
        return response()->json('ok',200);
    }

    public function updateResultTaken(Request $request, $id){
        $this->validate($request, [
            'results' => 'required|string',
        ]);
        $test = Test::findOrFail($id);
        $test->results = $request->results;
        $test->generated_by = auth()->user()->name;
        $test->save();
        return response()->json('ok',200);
    }

    public function sendTestMail(Request $request){

        $batch = $request->batch;
        if($batch){
            $results = Test::query()->with(['patient'])
                        ->where('batch', $batch)->first();

            $full_name = $results->patient->first_name.' '.$results->patient->middle_name.' '.$results->patient->last_name;
            $email = $results->patient->email;

            $content = [
                'fullname' => $full_name,
                'email' => $email,
                'title' => 'Test Result',

                'url' => 'https://lab.gtihc.com/get-test-mail/'.$batch
            ];
            // dd($content);
            try {
                Mail::to($email)->send(new TestEmail($content));
                return response()->json('ok',200);
            } catch (\Exception $e) {
                echo 'Error - '.$e;
            }
        }else {
         return response('Something went wrong, please try again', 500);

        }
    }


    public function updateMixTaken(Request $request, $id){
        $this->validate($request, [
            'mixed_status' => 'required|integer'
        ]);
        $test = Test::findOrFail($id);
        $test->mixed_status = 1;
        $test->mixed_by = auth()->user()->name;
        $test->save();
        return response()->json('ok',200);
    }

    public function updateControlTaken(Request $request, $id){

        // dd($request->formData->file);
        // $this->validate($request, [
        //     'file' => 'required|file|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf|max:500000',
        //     'comments' => 'string',
        // ]);
        $test = Test::findOrFail($id);
         if($request->hasFile('file')) {
             $file_name = time().'_'.$request->file->getClientOriginalName();
             $file_path =Storage::url($request->file('file')->storeAs('uploads', $file_name, 'public'));
             $test->attachment = $file_path;
             $test->controlled_status = 1;
             $test->comments = $request->comments;
             $test->controlled_by = auth()->user()->name;
             $test->update();
             return response()->json('ok',200);
         }
         return response('Something went wrong, please try again', 500);
    }
        public function search(Request $request){
        $searchWord = $request->get('');
        $test = Test::with('patient')->where(function($query) use ($searchWord){
            $query->where('tests.requested_test', 'LIKE', "%$searchWord%");
        })->latest()->paginate(5);
        $patient = Patient::where(function($query) use ($searchWord){
            $query->where('first_name', 'LIKE', "%$searchWord%")
            ->orWhere('middle_name', 'LIKE', "%$searchWord%")
            ->orWhere('last_name', 'LIKE', "%$searchWord%")
            ;
        })->latest()->get();
        //$data = $test->merge($patient);
        return response()->json([
            'tests' => $test
        ], 200);

    }


}
