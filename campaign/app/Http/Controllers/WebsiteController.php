<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Websites;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $websites = Websites::all();
        return view("websites.index", ["websites"=>$websites]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("websites.create");
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
            'name'  => 'required',
            'slug'  => 'required',
            'url'   => 'required|url',
        ]);


        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }

        Websites::create($data);

        return redirect()->route('websites.index')
            ->with('success', 'Website created successfully.');
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
    public function edit(Websites $website)
    {

        return view("websites.edit", [
            "website"       => $website
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Websites $website)
    {
        $request->validate([
            'name'  => 'required',
            'slug'  => 'required',
            'url'   => 'required|url'
        ]);

        $data = $request->all();
        if(isset($data['_token'])) {
            unset($data['_token']);
        }

        $website->update($data);

        return redirect()->route('websites.index')
            ->with('success', 'Website updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Websites $website)
    {
        $website->delete();

        return redirect()->route('websites.index')
            ->with('success', 'Website deleted successfully');
    }
}
