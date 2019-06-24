<?php

namespace App\Http\Controllers\API;

use App\Invoice;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\UpdateInvoiceRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoicesControllerAPI extends Controller
{
    public function index(Request $request) {
        $state = $request->state == null ? "pending" : $request->state;

        $query = Invoice::where('invoices.state', $state)->with('meal.waiter');

        if ($request->sort) {
            $sortBy = explode('|', $request->sort);
            $param = explode('.',$sortBy[0]);
            if($param[0] === "meal"){
                if($param[1] === "waiter"){
                    $query = $query
                        ->join('meals', 'meals.id', 'invoices.meal_id')
                        ->join('users', 'users.id', 'meals.responsible_waiter_id')
                        ->orderBy('users.'.$param[2], $sortBy[1]);
                }elseif($param[1] === "table_number"){
                    $query = $query
                        ->join('meals', 'meals.id', 'invoices.meal_id')
                        ->orderBy($param[1], $sortBy[1]);
                }
            }else{
                $query = $query->orderBy($sortBy[0], $sortBy[1]);
            }
        }

        if($request->waiter){
            $query = $query->whereHas('meal', function ($query) use ($request) {
                $query->where('responsible_waiter_id', $request->waiter);
            });
        }

        if($request->date){
            $query = $query->whereHas('meal', function ($query) use ($request) {
                $query->whereDate("start", date('Y-m-d',strtotime($request->date)));
            });
        }
        return $query->paginate($request->per_page);
    }

    public function download(Request $request, $id){
        $invoice = Invoice::with('meal.waiter')->with('items')->findOrFail($id);
        $this->authorize('download', $invoice);
        $pdf = PDF::loadView('pdf.invoice', ["invoice" => $invoice]);
        return $pdf->download('invoice.pdf');
    }

    public function invoiceItems(Request $request, Invoice $invoice){
        $invoice->load('items:type,quantity,unit_price,sub_total_price,name');
        $items = $invoice->items;
        return new JsonResource($items);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return new InvoiceResource(Invoice::find($id));
    }

    function edit($id)
    {
        //
    }

    public function update(UpdateInvoiceRequest $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->validated());
        $invoice->state= 'paid';
        $invoice->meal->state= 'paid';
        $invoice->meal->save();
        $invoice->save();
        return response(new InvoiceResource($invoice),201);
    }

    public function destroy($id)
    {
        //
    }
}
