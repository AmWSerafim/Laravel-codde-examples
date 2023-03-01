<?php
namespace App\Classes;

use Ixudra\Curl\Facades\Curl;

use Illuminate\Support\Facades\Log;

class TaboolaAPI {
    public static $instance;
    private $access_token;
    private $account_id = 'rivmedia-cpa';

    public function __construct()
    {
        self::$instance = $this;
        $access_array = $this->getAccessToken();
        $this->access_token = $access_array->access_token;
    }

    protected function customLogRequest($request_title = "", $data = "", $api_call_url = "", $api_call_type = ""){
        $log = [
            'data'          =>  $data,
            'api_call'      =>  [
                'request_url'   => $api_call_url,
                'type'          => $api_call_type
            ]
        ];

        $log_string = var_export($log, true);
        Log::channel("taboolaAPICall")->info("API_request - ".$request_title." - ".$log_string);
    }

    protected function customLogResponse($request_title = "", $api_response=""){
        $log = [
            'api_response'  =>  json_decode($api_response),
        ];

        $log_string = var_export($log, true);
        Log::channel("taboolaAPICall")->info("API_response - ".$request_title." - ".$log_string);
    }

    protected function displayError($action, $name, $response){
        if($response->status != 200){
            $content = json_decode($response->content);
            echo '<p class="alert alert-danger">';
            echo $action.': "'.$name.'"<br>';
            echo 'Error: ';
            foreach($content as $item){
                print_r($item);
                echo '<br>';
            }
            echo '</p>';
        }
    }

    protected function getAccessToken(){

        $url = 'https://backstage.taboola.com/backstage/oauth/token';
        $response = Curl::to($url)
            ->withData([
                'client_id'     =>'57e505afac314395a0c00eb6d7fa62a1',
                'client_secret' =>'e986bb51bd284642b104b1ff2f5d793b',
                'grant_type'    =>'client_credentials'
            ])
            ->withContentType("application/x-www-form-urlencoded")
            ->post();

        $this->customLogRequest("getAccessToken", "", $url, "POST");
        $this->customLogResponse("getAccessToken", $response);

        return json_decode($response);
    }

    public function getAccessTokenTool(){
        return $this->access_token;
    }

    public function updateCustomAudience($campaign_id, $data, $account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/campaigns/'.$campaign_id.'/targeting/custom_audience';
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->post();

        $this->displayError('Custom Audience update', 'Campaign ID '.$campaign_id, $response);
        /*
        if($response->status != 200){
            echo '<p class="alert alert-danger">Error: '.$response->content.'<p>';
        }*/

        $this->customLogRequest("updateCustomAudience", $data, $url, "POST");
        $this->customLogResponse("updateCustomAudience", $response->content);

        return json_decode($response->content);
    }
    public function createAd($campaign_id, $data, $account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/campaigns/'.$campaign_id.'/items/';
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->post();

        $this->displayError('Ad create:', 'Campaign ID '.$campaign_id, $response);

        $this->customLogRequest("createAd", $data, $url, "POST");
        $this->customLogResponse("createAd", $response->content);

        return json_decode($response->content);
    }
    public function BulkCreateAd( $data, $account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/items/bulk/';
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->put();

        $this->displayError('Bulk Ad create:', 'Account ID '.$account_id, $response);

        $this->customLogRequest("BulkCreateAd", $data, $url, "PUT");
        $this->customLogResponse("BulkCreateAd", $response->content);

        return json_decode($response->content);
    }

    public function uploadImage($imgPath){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/operations/upload-image';
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
            ])
            ->withFile( 'file', $imgPath, 'image/jpg' )
            ->returnResponseObject()
            ->post();

        $this->displayError('Image upload:', '', $response);

        $this->customLogRequest("uploadImage", $imgPath, $url, "POST");
        $this->customLogResponse("uploadImage", $response->content);

        return json_decode($response->content);
    }

    public function getAd($campaign_id, $item_id, $account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/campaigns/'.$campaign_id.'/items/'.$item_id;
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getAd", "", $url, "GET");
        $this->customLogResponse("getAd", $response);

        return json_decode($response);
    }

    public function UpdateAd($campaign_id, $item_id, $data, $account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/campaigns/'.$campaign_id.'/items/'.$item_id;
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->post();

        $this->displayError('Update Ad:', 'Ad ID '.$item_id, $response);

        $this->customLogRequest("UpdateAd", $data, $url, "POST");
        $this->customLogResponse("UpdateAd", $response->content);

        return json_decode($response->content);
    }
    public function GetCustomAudience(){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$this->account_id.'/campaigns/8433390/targeting/custom_audience';
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("GetCustomAudience", "", $url, "GET");
        $this->customLogResponse("GetCustomAudience", $response);

        return json_decode($response);
    }
    public function createCampaign($data, $account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/campaigns/';
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->post();

        $this->displayError('Create Campaign:', 'Campaign name '.$data['name'], $response);

        $this->customLogRequest("createCampaign", $data, $url, "POST");
        $this->customLogResponse("createCampaign", $response->content);

        return json_decode($response->content);
    }
    public function getCountries(){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/resources/countries';
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getCountries", "", $url, "GET");
        if(isset(json_decode($response)->errorMessage)) {
            $this->customLogResponse("getCountries", $response);
        }

        return json_decode($response);
    }

    public function getAllowedAccounts(){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/users/current/allowed-accounts';
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getAllowedAccounts", "", $url, "GET");
        if(isset(json_decode($response)->errorMessage)) {
            $this->customLogResponse("getAllowedAccounts", $response);
        }

        return json_decode($response);
    }
    public function getAllCustomAudiences($account_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/universal_pixel/custom_audience_rule';
        $response = Curl::to('https://backstage.taboola.com/backstage/api/1.0/'.$account_id.'/universal_pixel/custom_audience_rule')
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getAllCustomAudiences", "", $url, "GET");
        if(isset(json_decode($response)->errorMessage)) {
            $this->customLogResponse("getAllCustomAudiences", $response);
        }

        return json_decode($response);
    }

    public function deleteCampaign($compaign_id){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$this->account_id.'/campaigns/'.$compaign_id;
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->delete();

        $this->customLogRequest("deleteCampaign", "", $url, "DELETE");
        $this->customLogResponse("deleteCampaign", $response);

        return json_decode($response);
    }
    public function getAllCampaigns(){

        $url = 'https://backstage.taboola.com/backstage/api/1.0/'.$this->account_id.'/campaigns/?fetch_level=R';
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getAllCampaigns", "", $url, "GET");
        if(isset(json_decode($response)->errorMessage)) {
            $this->customLogResponse("getAllCampaigns", $response);
        }

        return json_decode($response);
    }
}
new TaboolaAPI();
