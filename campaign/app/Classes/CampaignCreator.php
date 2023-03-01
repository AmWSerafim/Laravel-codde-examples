<?php
namespace App\Classes;

use App\Models\Accounts;
use App\Models\Geos;
use Illuminate\Support\Facades\Storage;
use App\Models\Options;
use Ixudra\Curl\Facades\Curl;

class CampaignCreator {
    public static $instance;
    private static $allCPCs;
    private static $adsCombinations;
    private $devices = [
        "mobile_tablet"=>[
            "label" => "Mobile + Tablet",
            "slug" => "mobile",
            "devices" => "ALL",
            "exclude_operating_systems" => "iOS",

        ],
        "desktop"=>[
            "label" => "Desktop",
            "slug" => "desktop",
            "devices" => "ALL",
        ],

    ];
    public function __construct()
    {
        self::$instance = $this;
    }
    /**
     *
     * @return Company[]
     */
    public function generate($site, $choosedAccs_id, $name, $selectedDevices, $geos, $sandbox, $audience, $branding_text = "", $adsImages = [], $adsImagesLinks = [], $adsTitles = [], $adsUrl = "", $adProviders = [], $ad_prefix = "")
    {
        $ad_platforms_taboola = true;
        $ad_platforms_outbrain = true;
        $adsCombinations = [];
        if(!empty($adsTitles[0])) {
            $adsCombinations = $this->GenerateAdsCombinations( $adsImages,$adsImagesLinks, $adsTitles, $adsUrl, $ad_prefix);
        }

        self::$adsCombinations = $adsCombinations;
        $campaigns = $this->GenerateCampaigns($site, $choosedAccs_id, $name, $selectedDevices, $geos, $sandbox, $audience, $branding_text, $adsCombinations, $adProviders);
        $campaigns = $this->CampaignCreation($campaigns);
//        if($ad_platforms_taboola) {
//            $campaigns = $this->CampaignTaboolaCreation($campaigns);
//        }
//        if($ad_platforms_outbrain) {
//            $campaigns = $this->CampaignCreation($campaigns);
//        }
        return $campaigns;
    }

    private function CampaignCreation($campaigns){
        $campaigns = $this->createCampaignsAPI($campaigns);
        $ads = [];

        foreach ($campaigns as  $campaign) {
            if($campaign->getAds()) {
                $ads[$campaign->adProvider] =  array_merge(isset($ads[$campaign->adProvider]) ? $ads[$campaign->adProvider] : [] , $campaign->getAds());

            }
        }

        $taboola_accounts = [];
        $outbrain_accounts = [];
        foreach($campaigns as $campaign){
            if($campaign->adProvider == 'taboola'){
                if(!in_array($campaign->account_id, $taboola_accounts)){
                    $taboola_accounts[] = $campaign->account_id;
                }
            } elseif($campaign->adProvider == 'outbrain'){
                if(!in_array($campaign->account_id, $outbrain_accounts)){
                    $outbrain_accounts[] = $campaign->account_id;
                }
            }
        }

        if(isset($ads['outbrain'])) {
            $this->CreateAdsAPIOutbrain($ads['outbrain']);
            /*
            foreach($outbrain_accounts as $account) {
                $this->CreateAdsAPIOutbrain($ads['outbrain']);
            }
            */
        }
        if(isset($ads['taboola'])) {
            foreach($taboola_accounts as $account) {
                $campaignIds = [];
                foreach ($campaigns as $key => $campaign) {
                    if($campaign->campaignId) {
                        if($campaign->adProvider == "taboola" && (!in_array($campaign->campaignId, $campaignIds)) && $campaign->account_id == $account) $campaignIds[] = $campaign->campaignId;
                    }
                }
                $this->CreateAdsAPITaboola($campaignIds, $account);
            }
        }

        return $campaigns;

    }


    /**
     *
     * @return Company[]
     */
    public function GenerateCampaigns($site, $account_ids, $name, $devices, $geos, $sandbox = TRUE, $audiences = [], $branding_text = "", $ads = [], $adProviders = []) {
        $companies = [];
        foreach ($account_ids as $adProvider => $accounts) {
            if($accounts) {
                foreach ($accounts as $account_id) {
                    $company = new Company();
                    $company->sandbox = $sandbox;
                    $company->name = $name;
                    $company->site = $site;
                    $company->account_id = $account_id;
                    $company->adProvider = $adProvider;
                    if (count($audiences) > 0 && isset($audiences[$account_id])) {
                        array_push($audiences[$account_id], "");
                        $companies = array_merge($companies, $this->multiplyCompaniesBy([$company], $audiences[$account_id], "audience_id"));
                    } else {
                        $companies[] = $company;
                    }
                }
            }
        }

        if(count($devices) > 0) {
            $companies = $this->multiplyCompaniesBy($companies, $devices, "device");
        }
        if(count($geos) > 0) {
            $companies = $this->multiplyCompaniesByGeos($companies, $geos);
        }
//        if(count($adProviders) > 0) {
//            $companies = $this->multiplyCompaniesBy($companies, $adProviders, "adProvider");
//        }
        if(count($ads) > 0) {
            $companiesAfter = [];
            foreach ($companies as $company) {
                $tmpCompany = clone $company;
                foreach ($ads as $ad) {
                    $tmpCompany->addAd(clone $ad);
                }
                $companiesAfter[] = $tmpCompany;
            }
            $companies = $companiesAfter;

        }

        $options = Options::first();

        $tmp_companies = [];
        $allCPC =  $this->getAllCPCs($site);
        foreach($companies as $key => $company){
            if(!empty($company->audience_id)){
                $tmp_companies[$key] = $this->reformatAudiences($company);
            } else {
                $tmp_companies[$key] = $company;
            }

            if($company->adProvider == 'taboola'){
                $tracking_code = $options->tracking_taboola;
                $tmp_companies[$key]->branding_text = $branding_text;
            } elseif ($company->adProvider == 'outbrain'){
                $tracking_code = $options->tracking_outbrain;
            } else {
                $tracking_code = "";
            }
            $tmp_companies[$key]->setTrackingCode($tracking_code);
            $locale = strtolower($tmp_companies[$key]->geo_iso);
            $type = strtolower($tmp_companies[$key]->device);
            $devices = config('app.devices');
            $type = $devices[$tmp_companies[$key]->device]['cpcType'];


            if(!empty($tmp_companies[$key]->audience_id)){
                $type = $type."_rtg";
            }
            foreach ($allCPC as $cpc) {
                if($cpc->type == $type && $cpc->locale == $locale) {
                    $tmp_companies[$key]->setCPC($cpc->cpc);
                }
            }
            //$tmp_companies[$key]->setCPC();
        }
        $companies = $tmp_companies;

        //dump($companies);

        return $companies;
    }

    private function multiplyCompaniesByGeos($companies, $data) {
        $companiesAfter = [];
        foreach ($companies as $company) {
            $local_account = Accounts::where('api_account_id', $company->account_id)->first();
            $related_geos = $local_account->geos()->allRelatedIds()->toArray();
            foreach ($data as $item) {
                $local_geo_id = Geos::where('slug', $item)->first()->id;
                 if(in_array($local_geo_id,  $related_geos)) {
                    $tmpCompany = clone $company;
                    $tmpCompany->geo_iso = $item;
                    $companiesAfter[] = $tmpCompany;
                }
            }
        }
        $companies = $companiesAfter;
        return $companies;
    }

    private function multiplyCompaniesBy($companies, $data, $fieldName) {
        $companiesAfter = [];
        foreach ($companies as $company) {
            foreach ($data as $item) {
                $tmpCompany = clone $company;
                $tmpCompany->{$fieldName} = $item;
                $companiesAfter[] = $tmpCompany;
            }
        }
        $companies = $companiesAfter;
        return $companies;
    }

    private function getAllCPCs($site) {
        if(!empty(self::$allCPCs)) return self::$allCPCs;
        $token = "10IpIWlURcibSHGBmZsgI7npAbao6KmA46cicacxMvuF1ADNY700ACFTo4jTsNGqH3";
        $allowed_sites = ['soolide','dailybreak','soohealthy'];

        $site = strtolower($site);
        if(!in_array($site, $allowed_sites)){
            $site = 'soolide';
        }


        //dump([$site,$locale,$type]);

        $request_url = "https://tools.bro-media.net/api.php?site=".$site."&token=".$token;

        //dump($request_url);

        $response = Curl::to($request_url)
            ->withHeaders([
                'Content-Type'  => 'application/json'
            ])
            ->asJsonRequest()
            ->get();

        //dump($response);
        $result = json_decode($response);
        $result = $this->removeBRLFromCPCs($result);
        return self::$allCPCs = $result;

    }
    private function removeBRLFromCPCs($allCPCs){
        foreach ($allCPCs as $key => $cpc) {
            if($cpc->currency == "brl") {
                unset($allCPCs[$key]);
            }
        }
        return $allCPCs;
    }
    public function GenerateAdsCombinations($adsImages = [], $adsImagesLinks = [],$adsTitles = [], $adsUrl = "", $prefix="") {

        $ads = [];

        foreach($adsImagesLinks as $key => $image){
            if(isset($adsImages[$key]) && !empty($adsImages[$key])){
                $image = $adsImages[$key];
                $path = $image->store('/images/tmp');
                foreach ($adsTitles as $title) {
                    $ad = new Ad();
                    $ad->title = $title;
                    $ad->image_path = $path;
                    $ad->url = $adsUrl;
                    $ad->prefix = $prefix;
                    $ad->image_content_type = $image->getMimeType();
                    $ad->image_content = file_get_contents($image->getRealPath());
                    $ads[] = $ad;
                }
            } else {
                foreach ($adsTitles as $title) {
                    $ad = new Ad();
                    $ad->title = $title;
                    $ad->image_url = $image;
                    $ad->url = $adsUrl;
                    $ad->prefix = $prefix;
                    $ads[] = $ad;
                }
            }
        }

        return $ads;
    }

    /**
     * @param Company[] $campaigns
     */
    private function createCampaignsAPI($campaigns)
    {
        foreach ($campaigns as $key=>$campaign) {
            $campaigns[$key]->CreateAPI();
        }
        return $campaigns;
    }
    private function testBulkCreation($campaignIds, $account_id) {
        $imagesUploaded = [];

        $data['campaign_ids'] = $campaignIds;

        foreach (self::$adsCombinations as $key=> $adsCombination) {
            if (!isset($imagesUploaded[$adsCombination->image_path]) && empty($adsCombination->image_url)) {
                $storagePath = Storage::disk('local')->path($adsCombination->image_path);
                $image = TaboolaAPI::$instance->uploadImage($storagePath);
                $imagesUploaded[$adsCombination->image_path] = $image->value;
                Storage::delete($adsCombination->image_path);
            }
            if (empty(self::$adsCombinations[$key]->image_url)) self::$adsCombinations[$key]->image_url = $imagesUploaded[$adsCombination->image_path];
            $data['items'][] =  [
                "url"=> $adsCombination->url,
                "thumbnail_url"=> $adsCombination->image_url,
                "title" => $adsCombination->title,
            ];
        }


       $allAdsCreated =  TaboolaAPI::$instance->BulkCreateAd($data, $account_id);
        if(isset($allAdsCreated->message)) {
            var_dump("Problem with ad bulk creation: ".$allAdsCreated->message);
            if(isset($allAdsCreated->message_code)) {
                var_dump("Mess Code: ".$allAdsCreated->message_code);
            }

        }
        return $allAdsCreated;
    }
    private function CreateAdsAPITaboola($campaignIds, $account_id)
    {
        return $this->testBulkCreation($campaignIds, $account_id);


        // TO DO: need recheck this second time update but status update on first round triggers error and don't update Ad at all
        $updated_ads_count = $this->updateAdsStatus($ads, $account_id);
    }

    private function updateAdsStatus($ads, $account_id) {
        $adsUpdated = 0;
        foreach ($ads as $key => $ad) {
            $dataUpdate = [
                "is_active" => false
            ];
            $resultUpdate = TaboolaAPI::$instance->updateAd($ad->campaignId, $ad->apiID, $dataUpdate, $account_id);
            $adsUpdated++;
        }
        return $adsUpdated;
    }


    private function CreateAdsAPIOutbrain($ads)
    {
        // TODO: look into logic for do not create ADs for not created campaigns
        foreach ($ads as $key => $ad) {
            //dump($ad);
            if (!$ad->campaignId) { // debug IF in case if campaign not created
                //dump($ad);
                //dump("No campaignId");
                unset($ads[$key]);
            } else {
                $data = [
                    "url" => $ad->url,
                    "text" => trim($ad->prefix) . " " . trim($ad->title),
                    "enabled" => true,
                    //"sectionName" => "sectionName"
                ];
                if (!empty($ad->image_content)) {
                    $data["imageMetadata"]["stream"] = $ad->image_content;
                    $file = [
                        'content' => $ad->image_content
                    ];
                } else {
                    $data["imageMetadata"]["url"] = $ad->image_url;
                    $file = [];
                }

                if(!$data['enabled']){
                    $data['enabled'] = 'false';
                }

                $result = OutbrainAPI::$instance->createAd($ad->campaignId, $data, $file);
                if (isset($result->id)) {
                    $ads[$key]->apiID = $result->id;
                }

            }
        }
        //dump($ads);
        //var_dump($adsList);
    }

    public function reformatAudiences($company){

        $audience_data = explode("|", $company->audience_id);
        $company->audience_id = $audience_data[0];
        $company->audience_slug = $audience_data[1];

        return $company;
    }
}
new CampaignCreator();
