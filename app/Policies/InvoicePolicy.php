<?php

namespace App\Policies;

use App\Invoice;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function download(User $user, Invoice $invoice){
        return $invoice->state == "paid" && ($user->type == "manager" || $user->type == "cashier");
    }
}
