<?php
namespace App\Classes;

use App\Models\Accounts;
use App\Models\Geos;
use Illuminate\Support\Carbon;
use Ixudra\Curl\Facades\Curl;

class Company {
    public $name;
    public $account_id;
    public $audience_id;
    public $audience_slug;
    public $device;
    public $site;
    public $operating_systems;
    public $geo_iso;
    public $campaignId;
    public $branding_text;
    public $sandbox = TRUE;

    /**
     * Options: taboola, outbrain
     */
    public $adProvider;

    private $cpc;
    public $realcpc = null;
    private $tracking_code;
    private $daily_ad_delivery_model = "STRICT";
    private $daily_cap = 20;
    private $spending_limit = 100000;
    private $spending_limit_model = "MONTHLY";
    private $marketing_objective = "DRIVE_WEBSITE_TRAFFIC";

    private $tabula_sandbox_account = "rivmedia-cpa";
    private $outbrain_sandbox_account = "00c308502516b816d7cd6aa348d94f30b0";

    /**
     * @var Ad[];
     */
    public  $ads;

    /**
     * Create this company by API request
     */
    public function CreateAPI(){
        if($this->adProvider == "taboola") {
            $this->createTaboola();
        } else if($this->adProvider == "outbrain") {
            $this->createOutbrain();
        }
    }

    public function createTaboola()
    {

        if($this->cpc) {
            if(($this->sandbox && $this->account_id == $this->getSandboxAccount("taboola")) || !$this->sandbox) {
                $deviceList = config('app.devices');
                $data = [
                    "name" => $this->getFullName(),
                    "branding_text" => $this->branding_text,
                    "cpc" => $this->cpc,//$this->cpc,
                    "daily_ad_delivery_model" => $this->daily_ad_delivery_model,
                    "daily_cap" => $this->daily_cap,
                    "spending_limit" => $this->spending_limit,
                    "spending_limit_model" => $this->spending_limit_model,
                    "marketing_objective" => $this->marketing_objective,
                    "is_active" =>  $this->isActive(),
                    "platform_targeting" => [
                        "type" => "INCLUDE",
                        "value" => $deviceList[$this->device]["platform_targeting"]
                    ],
                    "tracking_code" => $this->tracking_code
                ];
                if (isset($deviceList[$this->device]["exclude_operating_systems"])) {
                    $data["os_targeting"] = [
                        "type" => "EXCLUDE",
                        "value" => [
                            [
                                "os_family" => $deviceList[$this->device]["exclude_operating_systems"]
                            ],
                        ],
                    ];
                }
                if (isset($deviceList[$this->device]["include_operating_systems"])) {
                    $data["os_targeting"] = [
                        "type" => "INCLUDE",
                        "value" => [
                            [
                                "os_family" => $deviceList[$this->device]["include_operating_systems"]
                            ],
                        ],
                    ];
                }
                if ($this->geo_iso) {
                    $geo = Geos::where('slug', $this->geo_iso)->first();
                    $data["country_targeting"] = [
                        "type" => "INCLUDE",
                        "value" => explode(",", $geo->countries_ISO_list),
                    ];
                }
                //dump($data);
                $result = TaboolaAPI::$instance->createCampaign($data, $this->account_id);
                if (isset($result->http_status) && $result->http_status == 400) {
                    echo '<div class="alert alert-danger"><b>' . $this->getFullName() . " Error: </b>" . $result->message . "</div>";
                } else {
                    if ($result->id) {
                        $this->campaignId = $result->id;
                        if($this->ads) {
                            foreach ($this->ads as $key => $ad) {
                                $this->ads[$key]->campaignId = $result->id;
                                $this->ads[$key]->isActive = $this->isActive();
                            }
                        }

                    }
                    if (!empty($this->audience_id)) {
                        if($this->sandbox) {
                            if ($result->id && $this->audience_id && $this->account_id == $this->getSandboxAccount('taboola')) {
                                $this->createAudience($result->id, $this->account_id);
                            }
                        } else {
                            $this->createAudience($result->id, $this->account_id);
                        }
                    }
                }
            }
        }

      // $this->createAds($result->id);
    }
    public function createOutbrain()
    {
        //dump($this);
        $date = Carbon::now();
        if($this->cpc) {
            if(($this->sandbox && $this->account_id == $this->getSandboxAccount("outbrain")) || !$this->sandbox) {
                $deviceList = config('app.devices');
                $budgetData = [
                    "name" => "Budget For campaign " . $this->getFullName() . "_" . time(),
                    "runForever" => true,
                    "amount" => $this->daily_cap,
                    "type" => "DAILY",
                    "pacing" => "SPEND_ASAP",
                    "startDate" => $date->format("Y-m-d"),//"2021-02-26",
                    "dailyTarget" => $this->daily_cap,
                ];
                //dump($budgetData);
                $budget = OutbrainAPI::$instance->CreateBudget($budgetData, $this->account_id);
                //dump($budget);
                $data = [
                    "name" => $this->getFullName(),
                    "cpc" => $this->cpc."",//0.05,
                    "enabled" => $this->isActive(), // if sandbox ON - we send paused campaign
                    "budgetId" => $budget->id,
                    "objective" => "Traffic",
                    "targeting" => [
                        "excludeAdBlockUsers" => true,
                        "platform" => $deviceList[$this->device]['platform_targeting_outbrain'],
                        //                "locations"=> ["a7fd4b0f63f8437cf04690b481e5eab1"],
                    ],
                    "suffixTrackingCode" => $this->tracking_code,//"utm_source=obr&utm_campaign={{campaign_id}}&utm_medium=referral&utm_term={{publisher_name}}_{{section_name}}&utm_content={{ad_id}}&s_id={{section_id}}&cl={{ob_click_id}}",
                ];
                if ($this->geo_iso) {
                    $geo = Geos::where('slug', $this->geo_iso)->first();
                    if ($geo->countries_outbrain_ids_list) {
                        $data["targeting"]['locations'] = explode(",", $geo->countries_outbrain_ids_list);
                    }
                }
                if (isset($deviceList[$this->device]['operating_systems_outbrain'])) {

                    $data["targeting"]["operatingSystems"] = $deviceList[$this->device]['operating_systems_outbrain'];
                }
                if (!empty($this->audience_id)) {
                    if($this->sandbox) {
                        if ($this->audience_id && $this->account_id == $this->getSandboxAccount('outbrain')) {
                            $API_segments[] = $this->audience_id;
                            $data["targeting"]["customAudience"]["includedSegments"] = $API_segments;
                        }
                    } else {
                        $API_segments[] = $this->audience_id;
                        $data["targeting"]["customAudience"]["includedSegments"] = $API_segments;
                    }
                }
                //dump($data);
                $campaign = OutbrainAPI::$instance->CreateCampaign($data);
                if (isset($campaign->validationErrors) || isset($campaign->message)) {
                    OutbrainAPI::$instance->DeleteBudget($budget->id);
                    if(isset($campaign->validationErrors)) var_dump($campaign->validationErrors);
                    if(isset($campaign->message)) var_dump($campaign->message);
                } else {
                    if ($campaign->id) {
                        $this->campaignId = $campaign->id;
                        if($this->ads) {
                            foreach ($this->ads as $key => $ad) {
                                $this->ads[$key]->campaignId = $campaign->id;
                                $this->ads[$key]->isActive = $this->isActive();
                            }
                        }

                    }
                }
                OutbrainAPI::$instance->DeleteBudget($budget->id);
            }
        }

      // $this->createAds($result->id);
    }

    /**
     * @param Ad[] $ads
     */
    public function addAds($ads){
        $this->ads = $ads;
    }
    public function addAd(Ad $ad){
        $this->ads[] = $ad;
    }
    public function getAds(){
        return $this->ads;
    }
    public function createAds($campaign_id){
        $data = [
            "url" => "http://www.soolide.com/en/16720",
        ];

        $result =  TaboolaAPI::$instance->createAd($campaign_id, $data);
        return $result;
    }
    private function createAudience($campaign_id, $account_id){
        $data = [

            "collection" => [
                [
                    "collection" => [
                        $this->audience_id
                    ],
                    "type"=> "INCLUDE"
                ]
            ]
        ];
        $result =  TaboolaAPI::$instance->updateCustomAudience($campaign_id, $data, $account_id);
    }
    public function getFullName(){
        $deviceList = config('app.devices');
        $geo = Geos::where('slug', $this->geo_iso)->first();
        $account = Accounts::where("api_account_id", $this->account_id)->first();
        $fullName = $account->slug."_".$this->name."_".$deviceList[$this->device]['slug'];
        if($this->geo_iso) {
            $fullName .= "_".$geo->slug;
        }
        if($this->audience_id) {
            $fullName .= "_".$this->audience_slug;
        }
        return $fullName;
    }

    public function setCPC($cpc){
        if($this->adProvider == 'outbrain' && $cpc < 0.03)  {
            $this->realcpc = $cpc."";
            $cpc = 0.03;

        }
        $this->cpc = $cpc."";
    }

    public function isActive(){
        return !($this->realcpc || $this->sandbox);
    }
    public function getCampainCPC(){
        return $this->cpc;
    }

    public function setTrackingCode($string){
        $this->tracking_code = $string;
    }

    public function getSandboxAccount($type){
        if($type == "taboola"){
            return $this->tabula_sandbox_account;
        } elseif ($type == "outbrain"){
            return $this->outbrain_sandbox_account;
        } else {
            die("Incorrect Type provided");
        }
    }

}
