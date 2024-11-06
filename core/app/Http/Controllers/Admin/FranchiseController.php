<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Franchise;
use App\Models\State;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class FranchiseController extends Controller
{

    public function franchise()
    {
        $pageTitle  = 'Franchises';
        $franchises = Franchise::orderBy('name', 'asc')->paginate(getPaginate());
        $states     = State::orderBy('state_title', 'asc')->get();
        $districts  = District::orderBy('district_title', 'asc')->get();
        return view('admin.franchise.index', compact('pageTitle', 'franchises', 'states', 'districts'));
    }

    public function franchiseSave(Request $request)
    {
        $request->validate([
            'store'          => 'required',
            'state'            => 'required|min:0|integer',
            'district'            => 'required|min:0|integer',
            'commission'            => 'required|min:0|integer',
            'name'          => 'required',
            'mobile'          => 'required|numeric',
            'pan_number'          => 'required',
            'bank_name'          => 'required',
            'account_number'          => 'required',
            'ifsc_code'   => 'required',
        ]);

        $franchise = new Franchise();
        if ($request->id) {
            $franchise = Franchise::findOrFail($request->id);
        }

        $franchise->store          = $request->store;
        $franchise->state_id   = $request->state;
        $franchise->district_id         = $request->district;
        $franchise->commission            = $request->commission;
        $franchise->name          = $request->name;
        $franchise->mobile   = $request->mobile;
        $franchise->pan_number         = $request->pan_number;
        $franchise->bank_name            = $request->bank_name;
        $franchise->account_number            = $request->account_number;
        $franchise->ifsc_code            = $request->ifsc_code;
        $franchise->save();

        $notify[] = ['success', 'Franchise saved successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Franchise::changeStatus($id);
    }

    public function remove($id)
    {
        $franchise = Franchise::find($id);
        fileManager()->removeFile(getFilePath('gateway') . '/' . $franchise->image);
        $franchise->delete();
        $notify[] = ['success', 'Franchise removed successfully'];
        return back()->withNotify($notify);
    }
}
