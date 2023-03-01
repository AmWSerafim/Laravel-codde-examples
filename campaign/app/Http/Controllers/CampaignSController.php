<?php

namespace App\Http\Controllers;

use App\Classes\OutbrainAPI;
use App\Classes\TaboolaAPI;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class CampaignSController extends Controller
{
    protected function getAccessToken(){

        $response = Curl::to('https://backstage.taboola.com/backstage/oauth/token')
            ->withData([
                'client_id'     =>'57e505afac314395a0c00eb6d7fa62a1',
                'client_secret' =>'e986bb51bd284642b104b1ff2f5d793b',
                'grant_type'    =>'client_credentials'
            ])
            ->withContentType("application/x-www-form-urlencoded")
            ->post();

        return json_decode($response);
    }

    protected function getAccount($access_token){
        $response = Curl::to('https://backstage.taboola.com/backstage/api/1.0/users/current/account')
            ->withHeaders([
                'Authorization' => 'Bearer '.$access_token,
            ])
            ->get();

        return json_decode($response);
    }
/*
    protected function getAccountInNetwork($access_token, $account_id){
        curl --request GET \
        --url https://backstage.taboola.com/backstage/api/1.0/account_id/advertisers
    }
*/
    protected function createCampaign($access_token, $account_id){

        $response = Curl::to('https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/campaigns/')
            ->withData([
                "name"                  =>  "TestCampaign",
                "branding_text"         =>  "AlexTest",
                "cpc"                   =>  0.01,
                "spending_limit"        =>  10,
                "spending_limit_model"  =>  "MONTHLY",
                "marketing_objective"   =>  "DRIVE_WEBSITE_TRAFFIC",
                "is_active"             =>  false
            ])
            ->withHeaders([
                'Authorization' => 'Bearer '.$access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            //->withContentType("application/json")
            ->post();
        //return $response;
        return json_decode($response);
    }

    public function index(Request $request){

        return view('admin.campaign_s.index');
    }

    public function create(){

        $account_id = 'rivmedia-cpa';

        $access_array = $this->getAccessToken();
        $access_token = $access_array->access_token;
        //$access_token = 'CccXAAAAAAAAESFxAwAAAAAAGAEgAClwL3kadwEAADooMjMzNjkxZWI1ZjViZjEzNmQzNjUxMzgwZWIzZWY2MDg5OWQ0ZWE2OEAC::c65e5e::3fdec0';
        dump($access_token);

        dump($this->getAccount($access_token));

        dump($this->createCampaign($access_token, $account_id));
    }
    public function delete(){

        TaboolaAPI::$instance->deleteCampaign(8238011);


    }
    public function showAll(){

        //TaboolaAPI::$instance->deleteCampaign(8238011);
        $data = TaboolaAPI::$instance->getAllCampaigns();

    }


    public function deleteAllCompanies(){
        $data = TaboolaAPI::$instance->getAllCampaigns();
        var_dump($data);
        if($data->results) {
            $compaigns = $data->results;
            foreach ($compaigns as $compaign) {
                TaboolaAPI::$instance->deleteCampaign($compaign->id);
            }
        }
    }

    public function showAllBudgets(){

        //TaboolaAPI::$instance->deleteCampaign(8238011);
        $data = OutbrainAPI::$instance->getAllBudgets();
        var_dump($data);
    }
    public function deleteAllBudgets(){

        //TaboolaAPI::$instance->deleteCampaign(8238011);
        $data = OutbrainAPI::$instance->getAllBudgets();
        if($data->budgets) {
            $budgets = $data->budgets;
            foreach ($budgets as $budget) {
               $show= OutbrainAPI::$instance->DeleteBudget($budget->id);
               var_dump($show);
            }
        }
    }
}
