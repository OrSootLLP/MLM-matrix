<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Pin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class PinController extends Controller
{
    private $users;

    public function __construct()
    {
        $this->users = User::active()->get();
    }

    public function allPins()
    {
        $pageTitle = "All Pins";
        $pins      = Pin::searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        $users     = $this->users;
        return view('admin.pin.index', compact('pageTitle', 'pins', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'number' => 'required|integer|gt:0'
        ]);

        for ($i = 1; $i <= $request->number; $i++) {
            $pin          = new Pin();
            $pin->amount  = $request->amount;
            $pin->pin     = rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999) . '-' . rand(10000000, 99999999);
            $pin->details = "Created via admin";
            if ($request->user) {
                $pin->user_id = $request->user;
                $pin->status  = Status::YES;
            }
            $pin->save();

            if ($request->user) {
                $general = gs();
                $user = User::find($request->user);
                $user->balance += $pin->amount;
                $user->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $pin->amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '+';
                $transaction->remark       = 'epin';
                $transaction->details      = 'E-Pin recharge via ' . $pin->pin;
                $transaction->trx          = getTrx();
                $transaction->save();

                $deposit                  = new Deposit();
                $deposit->user_id         = $user->id;
                $deposit->method_code     = 0;
                $deposit->method_currency = $general->cur_text;
                $deposit->amount          = $pin->amount;
                $deposit->rate            = 1;
                $deposit->final_amount    = $pin->amount;
                $deposit->btc_amount      = 0;
                $deposit->btc_wallet      = "";
                $deposit->trx             = $transaction->trx;
                $deposit->try             = 0;
                $deposit->status          = Status::PAYMENT_SUCCESS;
                $deposit->save();

                notify($user, 'PIN_RECHARGE', [
                    'trx'          => $transaction->trx,
                    'pin_number'   => $pin->pin,
                    'amount'       => getAmount($pin->amount),
                    'post_balance' => showAmount($user->balance, currencyFormat: false),
                ]);
            }
        }

        $notify[] = ['success', 'Pin added Successfully'];
        return back()->withNotify($notify);
    }

    public function userPins()
    {
        $pageTitle = "All User Pins";
        $pins      = Pin::whereNotNull('generate_user_id')->searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        $users     = $this->users;
        return view('admin.pin.index', compact('pageTitle', 'pins', 'users'));
    }

    public function adminPins()
    {
        $pageTitle = "All Admin Pins";
        $pins      = Pin::whereNull('generate_user_id')->searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        $users     = $this->users;
        return view('admin.pin.index', compact('pageTitle', 'pins', 'users'));
    }

    public function usedPins()
    {
        $pageTitle = "All Used Pins";
        $pins      = Pin::where('status', Status::ENABLE)->searchable(['pin'])->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        $users     = $this->users;
        return view('admin.pin.index', compact('pageTitle', 'pins', 'users'));
    }

    public function unusedPins()
    {
        $pageTitle = "All Unused Pins";
        $pins      = Pin::where('status', Status::DISABLE)->searchable(['pin'])->with('createUser')->orderBy('id', 'desc')->paginate(getPaginate());
        $users     = $this->users;
        return view('admin.pin.index', compact('pageTitle', 'pins', 'users'));
    }
}
