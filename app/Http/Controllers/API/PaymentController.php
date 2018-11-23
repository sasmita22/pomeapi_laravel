<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::all();
        foreach ($payments as $payment) {
            $payment->view_project = [
                'href' => 'api/payment/'.$payment->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all payments',
            'payments' => $payments
        ];

        return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'desc' => 'required',
            'paid_at' => 'required',
            'total' => 'required',
            'invoice' => 'required',
        ]);
        

        $desc = $request->input('desc');
        $paid_at = $request->input('paid_at');
        $total = $request->input('total');
        $invoice = $request->input('invoice');


        $payment = new Payment([
            'desc' => $desc,
            'paid_at' => $paid_at,
            'total' => $total,
            'invoice' => $invoice,

        ]);

        if($payment->save()){
            $payment->payment = [
                'href' => 'api/payment/'.$payment->id,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'project created',
                'payment' => $payment
            ];
            return response()->json($message,201);
        }

        $response = [
            'msg' => 'Error during creation',
        ];

        return response()->json($response,404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = Payment::where('id',$id)->firstOrFail();
        $payment->payment = [
            'href' => 'api/v1/project',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Payment information',
            'payment' => $payment
        ];
        return response()->json($response,200);
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
        $this->validate($request,[
            'desc' => 'required',
            'paid_at' => 'required',
            'total' => 'required',
            'invoice' => 'required',
        ]);
        


        $desc = $request->input('desc');
        $paid_at = $request->input('paid_at');
        $total = $request->input('total');
        $invoice = $request->input('invoice');

        $payment = Payment::findOrFail($id);

        $payment->desc = $desc;
        $payment->paid_at = $paid_at;
        $payment->total = $total;
        $payment->invoice = $invoice;
    

        

        if(!$payment->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $payment->view_project = [
            'href' => 'api/v1/payment'.$payment->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Payment Updated',
            'project' => $payment
        ];

        return response()->json($response,200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);


        if(!$payment->delete()){

            return response()->json([
                'msg' => 'Deletion Failed'
            ],404);
        }

        $response = [
            'msg' => 'Payment deleted',
            'create' => [
                'href' => 'api/v1/project',
                'method' => 'POST',
                'params' => 'title, description,time'
            ]
        ];

        return response()->json($response,200);
    }
}
