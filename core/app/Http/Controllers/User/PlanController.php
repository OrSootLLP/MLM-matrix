<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\Mlm;
use App\Models\GatewayCurrency;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    function planIndex()
    {
        $pageTitle       = "Plans";
        $plans           = Plan::where('status', Status::ENABLE)->orderBy('price', 'asc')->paginate(getPaginate(15));
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        return view('Template::user.plan', compact('pageTitle', 'plans', 'gatewayCurrency'));
    }

    function planPurchase(Request $request)
    {
        $request->validate([
            'id'      => 'required',
            'gateway' => 'required'
        ]);

        $plan = Plan::where('status', Status::ENABLE)->findOrFail($request->id);
        $user = auth()->user();

        if ($user->plan_id && $user->plan->price > $plan->price) {
            $notify[] = ['error', 'Plan cannot be downgraded'];
            return back()->withNotify($notify);
        }

        if ($request->gateway != 'wallet') {
            $gate = GatewayCurrency::whereHas('method', function ($gate) {
                $gate->where('status', Status::ENABLE);
            })->where('method_code', $request->gateway)->first();

            if (!$gate) {
                $notify[] = ['error', 'Invalid gateway'];
                return back()->withNotify($notify);
            }

            if ($gate->min_amount > $plan->price || $gate->max_amount < $plan->price) {
                $notify[] = ['error', 'Plan price crossed gateway limit.'];
                return back()->withNotify($notify);
            }

            $data = PaymentController::insertDeposit($gate, $plan->price, $plan);
            session()->put('Track', $data->trx);
            return to_route('user.deposit.confirm');
        }

        if ($user->balance < $plan->price) {
            $notify[] = ['error', 'You\'ve no sufficient balance'];
            return back()->withNotify($notify);
        }

        $trx = getTrx();

        $mlm = new Mlm($user, $plan, $trx);
        $mlm->purchasePlan();

        $notify[] = ['success', ucfirst($plan->name) . ' plan purchased Successfully'];
        return to_route("user.plan.index")->withNotify($notify);
    }
}
