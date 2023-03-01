<?php

namespace App\Http\Controllers;

use App\Classes\TaboolaAPI;
use App\Classes\OutbrainAPI;
use App\Models\Geos;
use App\Models\Accounts;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use \Illuminate\Validation\Validator;


class GeosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $geos = Geos::all();
        return view("geos.index", ["geos"=>$geos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        /**
         * ToDo: save country list to some option table and refresh it once month
         */
        $countries = TaboolaAPI::$instance->getCountries();
        $accounts = Accounts::all();

        return view("geos.create", ["countries" => $countries->results, "accounts" => $accounts]);
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
            'name' => 'required',
            'slug' => 'required',
            'accounts' => 'required',
            'countries' => 'required_without:country_ids',
            'country_ids' => 'required_without:countries',
        ]);


        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }
        $accounts = $data['accounts'];

        if(!empty($data['countries'])) {
            $data['countries_ISO_list'] = implode(",", $data['countries']);
        } else {
            $data['countries_ISO_list'] = "";
        }

        if(!empty($data['country_codes'])){
            $data['countries_outbrain_ISO_list'] = implode(",", $data['country_codes']);
            $data['countries_outbrain_ids_list'] = implode(",", $data['country_ids']);
        } else {
            $data['countries_outbrain_ISO_list'] = "";
            $data['countries_outbrain_ids_list'] = "";
        }

        unset($data['accounts']);
        unset($data['countries']);
        unset($data['country_codes']);
        unset($data['country_ids']);

        $geo = Geos::create($data);
        $geo->accounts()->attach($accounts);

        return redirect()->route('geos.index')
            ->with('success', 'Geo created successfully.');
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
     * @param  Geos  $geo
     */
    public function edit(Geos $geo)
    {
        $countries = TaboolaAPI::$instance->getCountries();

        $outbrain_country_ids = [];
        $selected_outbrain_countries = [];
        $reformat_all_countries_array_for_outbrain = [];

        /* Tabula counties list used just as country codes => country names for display*/
        foreach($countries->results as $item){
            $reformat_all_countries_array_for_outbrain[$item->name] = $item->value;
        }

        if(!empty($geo->countries_outbrain_ids_list)){
            $outbrain_country_ids = explode(",",$geo->countries_outbrain_ids_list);
            $selected_outbrain_countries = explode(",",$geo->countries_outbrain_ISO_list);
        }

        $selectedCountries = explode(",",$geo->countries_ISO_list);

        $accounts = Accounts::all();

        $selected_accounts = $geo->accounts()->allRelatedIds();
        $selected_accounts_array = [];
        foreach ($selected_accounts as $item){
            $selected_accounts_array[] = $item;
        }

        return view("geos.edit", [
            "accounts"                      => $accounts,
            "selected_accounts"             => $selected_accounts_array,
            "countries"                     => $countries->results,
            "geo"                           => $geo,
            "selectedCountries"             => $selectedCountries,
            "selected_outbrain_countries"   => $selected_outbrain_countries,
            "outbrain_country_ids"          => $outbrain_country_ids,
            "countries_array_for_outbrain"  => $reformat_all_countries_array_for_outbrain,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Geos $geo
     */
    public function update(Request $request, Geos $geo)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'accounts' => 'required',
            'countries' => 'required_without:country_ids',
            'country_ids' => 'required_without:countries',
        ]);

        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }
        $accounts = $data['accounts'];

        if(!empty($data['countries'])) {
            $data['countries_ISO_list'] = implode(",", $data['countries']);
        } else {
            $data['countries_ISO_list'] = "";
        }

        if(!empty($data['country_codes'])){
            $data['countries_outbrain_ISO_list'] = implode(",", $data['country_codes']);
            $data['countries_outbrain_ids_list'] = implode(",", $data['country_ids']);
        } else {
            $data['countries_outbrain_ISO_list'] = "";
            $data['countries_outbrain_ids_list'] = "";
        }

        unset($data['countries']);
        unset($data['accounts']);
        unset($data['country_codes']);
        unset($data['country_ids']);

        $saved_accounts = $geo->accounts()->allRelatedIds();
        $saved_accounts_array = [];
        foreach($saved_accounts as $item){
            $saved_accounts_array[] = $item;
        }
        $detach_result = array_diff($saved_accounts_array, $accounts);
        $attach_result = array_diff($accounts, $saved_accounts_array);

        $geo->update($data);

        $geo->accounts()->detach($detach_result);
        $geo->accounts()->attach($attach_result);

        return redirect()->route('geos.index')
            ->with('success', 'Geo updated successfully');
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Geos $geo)
    {
        $geo->delete();

        return redirect()->route('geos.index')
            ->with('success', 'Geo deleted successfully');
        //
    }

    /**
     * Return countries list by search term
     *
     * @param string $search_term
     * @return array
     */
    public function outbrainCountrySearchAjax(Request $request){

        $search_term = $request->get("search_term");

        $outbrain_result = OutbrainAPI::$instance->getCountriesBySearch($search_term);

        $filtered_result = [];
        foreach($outbrain_result as $item){
            if($item->geoType == "Country") {
                $tmp_array = (array)$item;
                $tmp_array['label'] = $item->name;
                $tmp_array['value'] = $item->id."|".$item->code."|".$item->name;
                $filtered_result[] = (object)$tmp_array;
            }
        }

        return json_encode($filtered_result);
    }
}
