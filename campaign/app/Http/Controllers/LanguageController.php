<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geos;
use App\Models\Languages;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $languages = Languages::all();
        $geos = Geos::all();
        $geos_array = [];
        foreach($geos as $item){
            $geos_array[$item->id] = $item->slug;
        }

        $re_languages = [];
        foreach($languages as $key => $language){
            $lang_geos_array = explode(",", $language->geos_list);
            $re_lang_geos_array = [];
            foreach($lang_geos_array as $geo){
                /* IF added for support rows saved before with slug */
                if(!in_array($geo, $geos_array)){
                    $re_lang_geos_array[] = $geos_array[$geo];
                } else {
                    $re_lang_geos_array[] = $geo;
                }
            }
            $language->geos_list = implode(",", $re_lang_geos_array);
            $re_languages[$key] = $language;
        }

        return view("languages.index", ["languages"=>$re_languages, "geos"=>$geos_array]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $geos = Geos::all();
        return view("languages.create", ["geos"=>$geos]);
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
            'geos' => 'required',
        ]);


        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }

        $data['geos_list'] = implode(",", $data['geos']);

        unset($data['geos']);

        Languages::create($data);

        return redirect()->route('languages.index')
            ->with('success', 'Language created successfully.');
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
    public function edit(Languages $language)
    {
        $geos = Geos::all();
        $selected_geos = explode(",",$language->geos_list);

        return view("languages.edit", [
            "language"      => $language,
            "geos"          => $geos,
            "selected_geos" => $selected_geos
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Languages $language)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required',
            'geos' => 'required'
        ]);

        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }
        $data['geos_list'] = implode(",", $data['geos']);

        unset($data['geos']);

        $language->update($data);

        return redirect()->route('languages.index')
            ->with('success', 'Language updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Languages $language)
    {
        $language->delete();

        return redirect()->route('languages.index')
            ->with('success', 'Language deleted successfully');
    }
}
