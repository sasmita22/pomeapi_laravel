<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Invoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::all();
        foreach ($invoices as $invoice) {
            $invoice->view_invoice = [
                'href' => 'api/invoice/'.$invoice->invoice_id,
                'method' => 'GET'
            ];
        }

        $response = [
            'msg' => 'List of all Projects',
            'invoices' => $invoices
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
            'notes' => 'required',
            'total' => 'required',
            'status' => 'required',
            'due_date' => 'required',
            'project' => 'required',
        ]);
        
        $notes = $request->input('notes');
        $total = $request->input('total');
        $status = $request->input('status');
        $due_date = $request->input('due_date');
        $project = $request->input('project');



        $invoice = new Invoice([
            'notes' => $notes,
            'total' => $total,
            'status' => $status,
            'due_date' => $due_date,
            'project' => $project,

        ]);

        if($invoice->save()){
            $invoice->invoice = [
                'href' => 'api/invoice/'.$invoice->invoice_id,
                'method' => 'GET'
            ];

            $message = [
                'msg' => 'invoice created',
                'invoice' => $invoice
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
        $invoice = Invoice::where('invoice_id',$id)->firstOrFail();
        $invoice->invoice = [
            'href' => 'api/v1/invoice',
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Invoice information',
            'invoice' => $invoice
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
            'notes' => 'required',
            'total' => 'required',
            'status' => 'required',
            'due_date' => 'required',
            'project' => 'required',
        ]);
        


        $notes = $request->input('notes');
        $total = $request->input('total');
        $status = $request->input('status');
        $due_date = $request->input('due_date');
        $project = $request->input('project');

        $invoice = Invoice::findOrFail($id);

        $invoice->notes = $notes;
        $invoice->total = $total;
        $invoice->status = $status;
        $invoice->due_date = $due_date;
        $invoice->project = $project;
        


        if(!$invoice->update()){
            return response()->json([
                'msg' => 'Error during update'
            ],404);
        }

        $invoice->invoice = [
            'href' => 'api/invoice'.$invoice->invoice_id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Invoice Updated',
            'invoice' => $invoice
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
        $invoice = Invoice::findOrFail($id);


        if(!$invoice->delete()){
            return response()->json([
                'msg' => 'Deletion Failed'
            ],404);
        }

        $response = [
            'msg' => 'Invoice deleted',
            'create' => [
                'href' => 'api/v1/project',
                'method' => 'POST',
                'params' => 'title, description,time'
            ]
        ];

        return response()->json($response,200);
    }
}
