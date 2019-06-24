<!DOCTYPE html>
<html>
    <head>
        <style>
            body{
                font-family: -apple-system,BlinkMacSystemFont,
                    "Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,
                    "Helvetica Neue",sans-serif;
            }
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
        </style>
    </head>
    <body>
        <h1>Invoice #{{ $invoice->id }}</h1>
        <h3>Restaurant Manager</h3>
        <div style="text-align: right;">
            <p>Name: {{ $invoice->name }}</p>
            <p>NIF: {{ $invoice->nif }}</p>
            <p>Date: {{ $invoice->date }}</p>
        </div>
        <table>
            <tr>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Sub total</th>
            </tr>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ ucfirst($item->type) }}</td>
                    <td>{{ $item->pivot->quantity }}</td>
                    <td>{{ $item->pivot->unit_price }} €</td>
                    <td>{{ $item->pivot->sub_total_price }} €</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"><b>Total</b></td>
                <td>{{ $invoice->total_price }} €</td>
            </tr>
        </table>
        <div style="text-align: right;">
            <p>Signed by</p>
            <p>{{ $invoice->meal->waiter->name }}</p>
        </div>
    </body>
</html>