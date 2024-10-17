<?php

namespace App\Http\Controllers\User;

use App\Lib\Mlm;
use App\Models\Form;
use App\Models\User;
use App\Models\BvLog;
use App\Models\Plan;
use App\Models\Deposit;
use App\Constants\Status;
use App\Models\UserExtra;
use App\Lib\FormProcessor;
use App\Models\Withdrawal;
use App\Models\DeviceToken;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Models\UserRanking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Lib\GoogleAuthenticator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle        = 'Dashboard';
        $user             = auth()->user();
        $totalDeposit     = Deposit::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $submittedDeposit = Deposit::where('status', '!=', Status::PAYMENT_INITIATE)->where('user_id', $user->id)->sum('amount');
        $pendingDeposit   = Deposit::pending()->where('user_id', $user->id)->sum('amount');
        $rejectedDeposit  = Deposit::rejected()->where('user_id', $user->id)->sum('amount');

        $totalWithdraw     = Withdrawal::where('user_id', $user->id)->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $submittedWithdraw = Withdrawal::where('status', '!=', Status::PAYMENT_INITIATE)->where('user_id', $user->id)->sum('amount');
        $pendingWithdraw   = Withdrawal::pending()->where('user_id', $user->id)->count();
        $rejectWithdraw    = Withdrawal::rejected()->where('user_id', $user->id)->sum('amount');

        $totalRef   = User::where('ref_by', $user->id)->count();
        $totalBvCut = BvLog::where('user_id', $user->id)->where('trx_type', '-')->sum('amount');
        $totalLeft  = @$user->userExtra->free_left + @$user->userExtra->paid_left;
        $totalRight = @$user->userExtra->free_right + @$user->userExtra->paid_right;
        $totalBv    = @$user->userExtra->bv_left + @$user->userExtra->bv_right;
        $logs       = UserExtra::where('user_id', $user->id)->firstOrFail();


        return view('Template::user.dashboard', compact('pageTitle', 'user', 'totalDeposit', 'submittedDeposit', 'pendingDeposit', 'rejectedDeposit', 'totalWithdraw', 'submittedWithdraw', 'pendingWithdraw', 'rejectWithdraw', 'totalRef', 'totalBvCut', 'totalLeft', 'totalRight', 'totalBv', 'logs'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form      = Form::where('act', 'kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user      = auth()->user();
        $pageTitle = 'KYC Data';
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form           = Form::where('act', 'kyc')->firstOrFail();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData                   = $formProcessor->processFormData($request, $formData);
        $user->kyc_data             = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv                   = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;


        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.home');
    }


    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function bvLog(Request $request)
    {
        $user = auth()->user();
        if ($request->type) {
            if ($request->type == 'leftBV') {
                $pageTitle = "Left BV";
                $logs      = BvLog::where('user_id', $user->id)->where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'rightBV') {
                $pageTitle = "Right BV";
                $logs      = BvLog::where('user_id', $user->id)->where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'cutBV') {
                $pageTitle = "Cut BV";
                $logs      = BvLog::where('user_id', $user->id)->where('trx_type', '-')->orderBy('id', 'desc')->paginate(getPaginate());
            } else {
                $pageTitle = "All Paid BV";
                $logs      = BvLog::where('user_id', $user->id)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            }
        } else {
            $pageTitle = "BV LOG";
            $logs      = BvLog::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
        }
        return view('Template::user.bv_log', compact('pageTitle', 'logs'));
    }

    public function myReferralLog()
    {
        $pageTitle = "My Referral";
        $logs      = User::where('ref_by', auth()->id())->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.my_referral', compact('pageTitle', 'logs'));
    }

    public function binaryTree($user = null)
    {
        $pageTitle = 'Binary Tree';
        $user      = User::where('username', $user)->first();
        if (!$user) {
            $user = auth()->user();
        }
        $mlm  = new Mlm();
        $tree = $mlm->showTreePage($user);
        return view('Template::user.tree', compact('pageTitle', 'mlm', 'tree'));
    }

    public function balanceTransfer()
    {
        $general = gs();
        if ($general->balance_transfer != Status::ENABLE) {
            abort(404);
        }
        $pageTitle = 'Transfer Balance';
        return view('Template::user.balance_transfer', compact('pageTitle'));
    }

    public function transferConfirm(Request $request)
    {
        $general = gs();
        if ($general->balance_transfer != Status::ENABLE) {
            abort(404);
        }

        $request->validate([
            'username' => 'required|exists:users,username',
            'amount'   => 'required|numeric|gt:0'
        ]);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        $toUser = User::where('username', $request->username)->first();

        if ($user->id == $toUser->id) {
            $notify[] = ['error', 'You can\'t send money to your own account'];
            return back()->withNotify($notify);
        }

        $general    = gs();
        $amount     = $request->amount;
        $fixed      = $general->balance_transfer_fixed_charge;
        $percent    = $general->balance_transfer_per_charge;
        $charge     = ($amount * $percent / 100) + $fixed;
        $withCharge = $amount + $charge;

        if ($user->balance < $withCharge) {
            $notify[] = ['error', 'You have no sufficient balance'];
            return back()->withNotify($notify);
        }

        if ($general->balance_transfer_min > $amount) {
            $notify[] = ['error', 'Please follow minimum balance transfer limit'];
            return back()->withNotify($notify);
        }

        if ($general->balance_transfer_max < $amount) {
            $notify[] = ['error', 'Please follow maximum balance transfer limit'];
            return back()->withNotify($notify);
        }

        $user->balance -= $withCharge;
        $user->save();

        $trx                       = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $withCharge;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance transfer to ' . $toUser->username;
        $transaction->trx          = $trx;
        $transaction->save();

        notify($toUser, 'BALANCE_SEND', [
            'amount'       => showAmount($amount, currencyFormat: false),
            'charge'       => showAmount($charge, currencyFormat: false),
            'username'     => $toUser->username,
            'post_balance' => showAmount($user->balance, currencyFormat: false)
        ]);

        $toUser->balance += $amount;
        $toUser->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $toUser->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $toUser->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance receive from ' . $user->username;
        $transaction->trx          = $trx;
        $transaction->save();

        notify($toUser, 'BALANCE_RECEIVE', [
            'amount'       => showAmount($amount, currencyFormat: false),
            'username'     => $user->username,
            'post_balance' => showAmount($toUser->balance, currencyFormat: false)
        ]);

        $notify[] = ['success', 'Balance transferred successfully'];
        return back()->withNotify($notify);
    }

    public function ranking()
    {
        if (!gs()->user_ranking) {
            abort(404);
        }
        $pageTitle    = 'User Ranking';
        $userRankings = UserRanking::active()->get();
        $user         = auth()->user()->load('userRanking', 'referrals');
        return view('Template::user.user_ranking', compact('pageTitle', 'userRankings', 'user'));
    }

    public function addUser(Request $request)
    {
        // TODO: modify this in
        /* if (!gs()->add_user) {
            abort(404);
        } */
        $pageTitle    = 'Add User';
        $activeUser   = auth()->user();
        $plans        = Plan::where('status', Status::ENABLE)->get();

        if ($request->doSubmit) {
            $this->validator($request->all())->validate();
            $plan         = Plan::where('status', Status::ENABLE)->findOrFail($request->plan);

            if ($activeUser->balance < $plan->price) {
                $notify[] = ['error', 'You\'ve no sufficient balance'];
                return back()->withNotify($notify);
            }

            $user            = new User();
            $user->email     = strtolower($request->email);
            $user->firstname = $request->firstname;
            $user->lastname  = $request->lastname;
            $user->password  = Hash::make($request->password);
            $user->ref_by    = $activeUser->id;
            $user->kv = gs('kv') ? Status::NO : Status::YES;
            $user->ev = gs('ev') ? Status::NO : Status::YES;
            $user->sv = gs('sv') ? Status::NO : Status::YES;
            $user->ts = Status::DISABLE;
            $user->tv = Status::ENABLE;
            $user->save();

            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $user->id;
            $adminNotification->title     = 'New member registered';
            $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
            $adminNotification->save();

            $userExtras = new UserExtra();
            $userExtras->user_id = $user->id;
            $userExtras->save();

            $trx = getTrx();

            $mlm = new Mlm($user, $plan, $trx);
            $mlm->purchasePlan();


            $notify[] = ['success', 'User created successfully'];
            return to_route("user.add.user")->withNotify($notify);
        }

        return view('Template::user.add_user', compact('pageTitle', 'plans'));
    }


    protected function validator(array $data)
    {

        $passwordValidation = Password::min(6);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validate     = Validator::make($data, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|string|email|unique:users',
            'password'  => ['required', 'confirmed', $passwordValidation],
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required'
        ]);

        return $validate;
    }
}
