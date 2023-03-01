<?php

namespace App\Http\Controllers;

//use App\Imports\ExcelImport;
use Illuminate\Http\Request;
//use Maatwebsite\Excel\Facades\Excel;
use Excel;
use Illuminate\Support\Facades\Storage;


class ExcelController extends Controller
{
    public function index(){

        return view('admin.excel.index');

    }

    public function import(Request $request){

        $filePath = $request->file('payment-file');
        $fileName = $filePath->getClientOriginalName();

        $payment_path = $request->file('payment-file')->storeAs('uploads', $fileName, 'public');

        $theArray = Excel::toArray([], Storage::disk('public')->path($payment_path));

        $limit = 0;

        $cols_number = 0;
        foreach($theArray[0][0] as $value){
            if(!empty($value)){
                $cols_number++;
            }
        }

        //dump($theArray[0][4]);
        dump($cols_number);

        $result_araay = [];
        foreach($theArray[0] as $key => $value){
            if($limit == 10){
                break;
            }
            $result_araay[$key] = array_slice($value, 0, $cols_number);
            //dump($value);
            $limit++;
        }
        dd($result_araay);

        Excel::load(Storage::disk('public')->path($payment_path), function ($reader) {

            foreach ($reader->toArray() as $row) {
                dump($row);
            }
        });
        dd();

        $tmp = Excel::import(new ExcelImport, Storage::disk('public')->path($payment_path));

        dump($tmp);

        $_tmp = $tmp->collection();

        dd($_tmp);

    }
}
