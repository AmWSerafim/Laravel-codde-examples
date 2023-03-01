<?php
namespace App\Classes;

use Ixudra\Curl\Facades\Curl;
use App\Models\Options;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Log;

class OutbrainAPI {
    public static $instance;
    private $access_token;
    /**
     * ToDo: save it to database and refresh each month
     */
    //private $OB_token = "MTYxNDI0OTU1NDI0NTowN2VmNDdmYTlkYTU1MmZmZmM5NzRlMzVlNzhhZTAxNGY5NzAzM2Q3Y2IyNTM1Njk2NGFiZjI4OTYzZjg4OWQ4NmNjNTRhZTQzMzc5M2UzOGUyZjZjYjA1YWE2NmQ5NWFmODFmMTI5NmMyYWRiMTE5N2NjZjFhZTc4NGQ3MDY4NTNmYWVhNmIxMDQ5OGIzOWJkNGM1NTNmZTBiNDI5NTNiNzI4ZWNkODY5YWE2NjY5MWM2M2E5NGExZmViZWY1ZGI1Mjc1MjE0NTIyMzVjZmIxNDk0Y2VjMzQxNTRjZWU2N2MwZWNkYTQ1MDNmNTRhYzMzYjAwMjU0YTlmYWVhZTM5OTI3MDBlYjZmMDkwYzBiZjNlNGNmMjNhOTVhNGI5YzQ1M2IyODNiODRmYmEzNWE0ZmY0NGFlOTU4YjM5MDc2NTE4NjRhZTM3NDUwNWJlYTNiY2U5ZTllOWE3MDU4Yzk5MjY1Y2YyNGM3ODM2MzJlYzI3ZjA0ODA5YWQ3MDhlM2U1NDhkMGJhYzcyNjFmYzEzN2FmYjQ0NmZjYzVlN2Q4MDNlZjczOWIzNmQ0Y2I5ODViZTBhYWE3ODY2NmFiMTNiOTY3M2NjNzQ1ZjYwZmI4YmE0ZjE5MWM5YzI5NGJkMGNhYjQwZDM5ODk0ZTk5NGViMmM5ZDRjNjFjMzNlZDRmZTp7ImNhbGxlckFwcGxpY2F0aW9uIjoiQW1lbGlhIiwiaXBBZGRyZXNzIjoiNzcuMTIyLjEwMS4yMyIsImJ5cGFzc0FwaUF1dGgiOiJmYWxzZSIsInVzZXJOYW1lIjoiZXN0ZXhkZXZAZ21haWwuY29tIiwidXNlcklkIjoiMTA2NzAxMDMiLCJkYXRhU291cmNlVHlwZSI6Ik1ZX09CX0NPTSJ9OjQwYWE4NGMxOWJmYmQ0YTZjNzU4M2QyOTk0YmIxMDhiM2UwMjQ3ZWFlNTc5OTgzYjA1Mjk0Y2Y1YWE5MWM0MmE4ZWYwNjg4NTExMTZkNjNmMDA5Y2IwOTU1MmUxYzNjZTY5MzVmZDAxYzcwNmFjYjA2OGRmOWI5YzM2ZDkyYzY2";
    //private $OB_token = "MTYxNzAyODEzMTYyOTpkNWNmZjk0NTg0ZGZmOTU2ZTk0NDAxYTQwZDEwMzQwOTZhYWM4MTMwYTBkODJmZGFlNDIyMTBkNWYyYzI5N2UzYjk4ZDkxNjUwZGU3ZjAwMzNjZGUzNmM0ZmE3ZDdlMjlkODdhYjIwN2Q3NWQyNmE4NmM3ZjcxY2MzYTA1NmFjODkzYzg0NTk3NzYxZDkwYjE4MGQxZGY3NTM4NGY3NDE1ZDQ5OTM5ZjlhZmEzZmU3MGZmNmJmZjc0NjViNjg0MTEyNGQwMWIwNjJiNWNhY2YyNjE3ZmQ0MTQ1NDgxYjY4NDdiYTY4NGZiZjhlMjFlZWNkMjczZTQ0NzNjMzQ1MTMyY2I1NjUwMDlmNjRhMGNkMzgzMTNiMjljZjc2MjdmN2ZkYTJhMjlmNzMyOTkyZjI4MWQxNTE5ZThiMTE0NWY5ZTQyZGI4OTcyMzVlY2ZjZjEzOWU4NjliMThhMzZlN2UzNjI0ZjRiYTVkZTYwMWQ0YTBhZjNjZTM0ZmUwZjQ1ZGMwYWFmYzk2YWUxNjhhYzg3NmY0ODgzNTgxN2QwNjlkOWQ3YmEwN2VlMWI2OTVmM2RkNzlmMmVlOTI1YWMwN2RhOGQyMTM3ODE3NWVmNzIyYzg0OWYwYjIxNjNhYzk0YTJkNWVhMjY0NGU4MTk3ZWZlMTQyZGQ0OTBhZjhjMDlmYTp7ImNhbGxlckFwcGxpY2F0aW9uIjoiQW1lbGlhIiwiaXBBZGRyZXNzIjoiOTUuNDIuMTE5LjQyIiwiYnlwYXNzQXBpQXV0aCI6ImZhbHNlIiwidXNlck5hbWUiOiJlc3RleGRldkBnbWFpbC5jb20iLCJ1c2VySWQiOiIxMDY3MDEwMyIsImRhdGFTb3VyY2VUeXBlIjoiTVlfT0JfQ09NIn06Nzc3ZWYxNGMzMzBjNDIxNjBhNThkY2YxOTFmMjc3NjhmOWIyYTU0NDBjMGQ2ZWRmNGQyNmM4MGJjNTA5MDUyODNmOWZlYzljYzAwMTM0NzU4MGE2YjMzZWE1YjU0NDM4OGJiYjJiYjBlYjcwNzFhZjBmNjM3YWQzOTdkNTJmOWU=";
    private $OB_token = "";

    private $test_marketersID = "00c308502516b816d7cd6aa348d94f30b0";
    public function __construct()
    {
        $options = Options::first();
        $token_update_date = Carbon::parse($options->OB_token_update_date);
        $now = Carbon::now();
        if(empty($options->OB_token)){
            $result = $this->getAccessToken();
            $options->update([
                "OB_token" =>  $result['OB-TOKEN-V1'],
                "OB_token_update_date" => Carbon::now()
            ]);
            $this->OB_token = $result['OB-TOKEN-V1'];
        } elseif ($token_update_date->diffInDays($now) >= 28 ){
            $result = $this->getAccessToken();
            $options->update([
                "OB_token" =>  $result['OB-TOKEN-V1'],
                "OB_token_update_date" => Carbon::now()
            ]);
            $this->OB_token = $result['OB-TOKEN-V1'];
        } else {
            $this->OB_token = $options->OB_token;
        }

        self::$instance = $this;
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

    protected function customLogRequest($request_title = "", $data = "", $api_call_url = "", $api_call_type = ""){
        $log = [
            'data'          =>  $data,
            'api_call'      =>  [
                'request_url'   => $api_call_url,
                'type'          => $api_call_type
            ]
        ];

        $log_string = var_export($log, true);
        Log::channel("outbrainAPICall")->info("API_request - ".$request_title." - ".$log_string);
    }

    protected function customLogResponse($request_title = "", $api_response=""){
        $log = [
            'api_response'  =>  json_decode($api_response),
        ];

        $log_string = var_export($log, true);
        Log::channel("outbrainAPICall")->info("API_response - ".$request_title." - ".$log_string);
    }

    protected function getAccessToken(){

        $url = 'https://api.outbrain.com/amplify/v0.1/login';
        $response = Curl::to($url)
            ->withHeaders([
                'Authorization' => 'Basic '.base64_encode("estexdev@gmail.com:AlexeyCreationTool2"),
            ])
            ->get();

        $this->customLogRequest("getAccessToken", "", $url, "GET");
        $this->customLogResponse("getAccessToken", $response);


        return json_decode($response, true);
    }

    public function getCampaignsNamesByAccount($account_id){

        $limit = 10;
        $offset = 0;

        $results = [];
        do {
            $response = Curl::to('https://api.outbrain.com/amplify/v0.1/marketers/' . $account_id . '/campaigns?includeArchived=true&limit=' . $limit . '&offset=' . $offset)
                ->withHeaders([
                    'OB-TOKEN-V1' => $this->OB_token
                ])
                ->asJsonRequest()
                ->get();
            $result = json_decode($response);
             $offset += $limit;
            foreach ($result->campaigns as $item){
                array_push($results, $item->name);
            }
        } while($result->totalCount > $offset && $result->count == $limit);

        return $results;
    }

    public function CreateCampaign($data){

        $url = 'https://api.outbrain.com/amplify/v0.1/campaigns?extraFields=CustomAudience,Locations,InterestsTargeting,BidBySections,BlockedSites,PlatformTargeting,CampaignOptimization,Scheduling,IABCategories?New%20item=';
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->post();

        $this->displayError("Campaign Creation", "Campaign name ".$data['name'], $response);

        $this->customLogRequest("createCampaign", $data, $url, "POST");
        $this->customLogResponse("createCampaign", $response->content);

        $respJson = json_decode($response->content);

        return $respJson;
    }
    public function CreateTestCampaign(){


        $budget = $this->CreateBudget();
        $data = [
            "name" => "new campaign4",
            "cpc" => 0.05,
            "enabled" => false,

            "budgetId" => $budget->id,
            "objective" => "Traffic",
            "targeting" => [
                "excludeAdBlockUsers" => true,
                "platform" => [
                    "DESKTOP",
                    "MOBILE"
                ],
                "locations"=> ["a7fd4b0f63f8437cf04690b481e5eab1"],
                "operatingSystems"=> ["MacOs", "Windows"],
                "browsers"=> ["Chrome"]
            ],
            "suffixTrackingCode"=> "utm_source=obr&utm_campaign={{campaign_id}}&utm_medium=referral&utm_term={{publisher_name}}_{{section_name}}&utm_content={{ad_id}}&s_id={{section_id}}&cl={{ob_click_id}}",

        ];
        $response = Curl::to('https://api.outbrain.com/amplify/v0.1/campaigns?extraFields=CustomAudience,Locations,InterestsTargeting,BidBySections,BlockedSites,PlatformTargeting,CampaignOptimization,Scheduling,IABCategories?New%20item=')
            ->withData($data)
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            //->withContentType("application/json")
            ->post();
        //return $response;

        $respJson = json_decode($response);
        if(isset($respJson->validationErrors) || isset($respJson->message)) {
            $this->DeleteBudget($budget->id);
        }
        return $respJson;
    }

    public function CreateBudget($data, $account_id){

        $url = 'https://api.outbrain.com/amplify/v0.1/marketers/'.$account_id.'/budgets';
        $response = Curl::to($url)
            ->withData($data)
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->returnResponseObject()
            ->post();

        $this->displayError("Budget Creation", "Budget name ".$data['name'], $response);

        $this->customLogRequest("createBudget", $data, $url, "POST");
        $this->customLogResponse("createBudget", $response->content);

        return json_decode($response->content);
    }

    public function getAllBudgets(){

        $url = 'https://api.outbrain.com/amplify/v0.1/marketers/'.$this->test_marketersID.'/budgets';
        $response = Curl::to()
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getAllBudgets", "", $url, "GET");
        $this->customLogResponse("getAllBudgets", $response);

        return json_decode($response);
    }

    public function DeleteBudget($id){
/*
        $data = [
            "name" => "Budget For campaign #5",
            "runForever"=> true,
            "amount"=> 10,
            "type"=> "DAILY",
            "pacing"=> "SPEND_ASAP",
            "startDate"=> "2021-02-26",
            "dailyTarget"=> 1,
        ];
*/
        $url = 'https://api.outbrain.com/amplify/v0.1/budgets/'.$id;
        $response = Curl::to($url)
//            ->withData($data)
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->delete();

        $this->customLogRequest("deleteBudget", "", $url, "DELETE");
        $this->customLogResponse("deleteBudget", $response);

        return json_decode($response);
    }

    public function CreateAd($campaignID, $data, $file=""){

        $url = 'https://api.outbrain.com/amplify/v0.1/campaigns/' . $campaignID . '/promotedLinks';
        if(!empty($file)){
            $response = Curl::to($url)
                ->withHeaders([
                    'OB-TOKEN-V1' => $this->OB_token,
                    'Content-Type' => 'multipart/form-data; boundary=Z42If9uOveoJshwD8eifQy9jAo2ekKVq; charset=utf-8'
                ])
                ->withData("--Z42If9uOveoJshwD8eifQy9jAo2ekKVq
Content-Disposition: form-data; name=\"image\"
Content-Type: application/octet-stream
Content-Transfer-Encoding: binary

".$file['content']."
--Z42If9uOveoJshwD8eifQy9jAo2ekKVq
Content-Disposition: form-data; name=\"url\"
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

".$data['url']."
--Z42If9uOveoJshwD8eifQy9jAo2ekKVq
Content-Disposition: form-data; name=\"enabled\"
Content-Type: */*; charset=US-ASCII
Content-Transfer-Encoding: 8bit

".$data['enabled']."
--Z42If9uOveoJshwD8eifQy9jAo2ekKVq
Content-Disposition: form-data; name=\"text\"
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit

".$data['text']."
--Z42If9uOveoJshwD8eifQy9jAo2ekKVq--")
                ->returnResponseObject()
                ->post();

            $combined_array = [
                "file"  => $file,
                "data"  => $data,
            ];

            $this->displayError("Image upload", "Campaign ID ".$campaignID, $response);

            $combined_array['file']['content'] = "Binary file content was removed";
            $combined_array['data']['imageMetadata']['stream'] = "Binary file content was removed";
            $this->customLogRequest("createAd_upload", $combined_array, $url, "POST");
            $this->customLogResponse("createAd_upload", $response->content);
        } else {
            $response = Curl::to($url)
                ->withData($data)
                ->withHeaders([
                    'OB-TOKEN-V1' => $this->OB_token,
                    'Content-Type' => 'application/json'
                ])
                ->asJsonRequest()
                ->returnResponseObject()
                ->post();

            $this->displayError("Image upload from URL", "Campaign ID ".$campaignID, $response);

            $this->customLogRequest("createAd_link", $data, $url, "POST");
            $this->customLogResponse("createAd_link", $response->content);
        }

        return json_decode($response->content);
    }

    public function getCountriesBySearch($search_string = "", $limit = 20){
        //$search_string = "United States";
        //https://api.outbrain.com/amplify/v0.1/locations/search?term=germany&limit=6

        $url = 'https://api.outbrain.com/amplify/v0.1/locations/search?term='.$search_string.'&limit='.$limit;
        $response = Curl::to($url)
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getCountriesBySearch", "", $url, "GET");
        $this->customLogResponse("getCountriesBySearch", $response);

        return json_decode($response);
    }

    public function getAccounts(){

        $url = 'https://api.outbrain.com/amplify/v0.1/marketers';
        $response = Curl::to($url)
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])->asJsonRequest()
            ->get();

        $this->customLogRequest("getAllAccounts", "", $url, "GET");

        if(isset(json_decode($response)->message)){
            $this->customLogResponse("getAllAccounts", $response);
        }

        return json_decode($response);
    }

    public function getAccountAudiences($account_id){

        $url = 'https://api.outbrain.com/amplify/v0.1/marketers/'.$account_id.'/segments';
        $response = Curl::to('https://api.outbrain.com/amplify/v0.1/marketers/'.$account_id.'/segments')
            ->withHeaders([
                'OB-TOKEN-V1' => $this->OB_token,
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        $this->customLogRequest("getAccountAudiences", "", $url, "GET");

        if(isset(json_decode($response)->message)){
            $this->customLogResponse("getAccountAudiences", $response);
        }

        return json_decode($response);
    }
}
new OutbrainAPI();
