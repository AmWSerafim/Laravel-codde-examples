<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Classes\OutbrainAPI;
use App\Classes\TaboolaAPI;

use App\Models\Accounts;
use App\Models\Websites;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Accounts::all();

        $taboola_data = TaboolaAPI::$instance->getAllowedAccounts();
        $taboola_accounts = $taboola_data->results;

        $outbrain_data = OutbrainAPI::$instance->getAccounts();
        $outbrain_accounts = $outbrain_data->marketers;

        $re_accounts = [];
        foreach($accounts as $key => $value){
            $tmp_account = [];
            $tmp_account['id'] = $value->id;
            $tmp_account['name'] = $value->name;
            $tmp_account['slug'] = $value->slug;
            $tmp_account['platform'] = $value->platform;

            $tmp_account['stored_account'] = $value;
            if($value->platform == "taboola"){
                foreach($taboola_accounts as $item){
                    if($item->account_id == $value->api_account_id){
                        $tmp_account['account_name'] = $item->name;
                        break;
                    }
                }
            } elseif($value->platform == "outbrain"){
                foreach($outbrain_accounts as $item){
                    if($item->id == $value->api_account_id){
                        $tmp_account['account_name'] = $item->name;
                        break;
                    }
                }
            }

            $tmp_account["website_url"] = $value->website->url;

            $re_accounts[] = (object)$tmp_account;
        }

        //dump($re_accounts);
        //dd();
        return view("accounts.index", ["accounts"=>$re_accounts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $taboola_accounts = TaboolaAPI::$instance->getAllowedAccounts();
        $websites = Websites::all();

         return view("accounts.create",[
            "websites"          => $websites,
            "accounts"          => $taboola_accounts->results,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required',
            'slug'              => 'required',
            'platform'          => 'required',
            'api_account_id'    => 'required',
            'website_id'        => 'required',
        ]);


        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }

        Accounts::create($data);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Accounts $account)
    {
        $websites = Websites::all();

        if($account->platform == "taboola"){
            $data = TaboolaAPI::$instance->getAllowedAccounts();
            $accounts = $data->results;
        } elseif($account->platform == "outbrain"){
            $data = OutbrainAPI::$instance->getAccounts();
            $accounts = $data->marketers;
        } else {
            $accounts = [];
        }

        return view("accounts.edit",[
            "account"   => $account,
            "websites"  => $websites,
            "accounts"  => $accounts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Accounts $account)
    {
        $request->validate([
            'name'              => 'required',
            'slug'              => 'required',
            'platform'          => 'required',
            'api_account_id'    => 'required',
            'website_id'        => 'required',
        ]);


        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }

        $account->update($data);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Accounts $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully');
    }

    public function generateAccountsSelect(Request $request) {

        $platform = $request->get('platform');

        if($request->get('selected') !== NULL ){
            $selected = $request->get('selected');
        } else {
            $selected = "";
        }

        if($platform == "taboola"){
            $data = TaboolaAPI::$instance->getAllowedAccounts();
            $accounts = $data->results;
        } elseif($platform == "outbrain"){
            $data = OutbrainAPI::$instance->getAccounts();
            $accounts = $data->marketers;
        } else {
            $accounts = [];
        }

        return view('accounts.accountsSelect', [
                "platform"  => $platform,
                "accounts"  => $accounts,
                "selected"  => $selected
            ]
        );
    }
}
