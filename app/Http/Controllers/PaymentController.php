<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Payment;

class PaymentController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $redirectTo = '/';
    protected $module = 'payment';


    public function __construct()
    {
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'transaction' => ['required'],
            'payment-type' => ['required'],
        ]);
    }

    protected function create(Request $request)
    {
        $transaction = DB::table('transaction')->where('transaction_id', $request['transaction']);
        $item = DB::table('item')->where('item_id', $request['item']);
        if ($item->get()->first()->item_stock > 0) {
            $this->validator($request->all())->validate();
            $payment = Payment::create([
                'transaction_id' => $request['transaction'],
                'payment_type_id' => $request['payment-type'],
                'payment_timestamp' => date('Y-m-d H:i:s')
            ]);
            $payment->save();
            $item->decrement('item_stock', 1);
            $transaction->update(['status_id' => 2]);
        }
        return back();
    }
}
