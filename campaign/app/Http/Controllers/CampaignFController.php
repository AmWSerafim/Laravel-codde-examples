<?php

namespace App\Http\Controllers;

use App\Classes\Ad;
use App\Classes\TaboolaAPI;
use App\Classes\OutbrainAPI;
use App\Classes\Company;
use App\Classes\CampaignCreator;
use App\Models\Websites;
use App\Models\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Geos;
use App\Models\Languages;
use Ixudra\Curl\Facades\Curl;

class CampaignFController extends Controller
{
    private $sites = [
      "Soolide",
      "Soo-Healthy",
      "Dailybreak"
    ];
    private $accounts = [
        "Soolide"=>[
            46 => [ "name" => "Wearesolideltd - Soolide", "shortcode" => ""],
            92 => ["name" =>"Wearesolideltd - New Soolide", "shortcode" => "SL"],
            99 => ["name" =>"Wearesolideltd - Soolide - Emerging", "shortcode" => "ESL"],
            103 => ["name" =>"RIV MEDIA - Soolide", "shortcode" => "RSL"],
            118 => ["name" =>"Wearesolideltd - Soolide - FR", "shortcode" => "FSL"],
            192 => ["name" =>"Wearesolideltd - Soolide - DE", "shortcode" => "DSL"],
        ],

    ];

    public function index(Request $request)
    {
        $geoList = Geos::all();

        $accountsIdToSlug = config('app.accountsIdToSlug');
        $devices = config('app.devices');
        //$languages = config('app.languages');

        $languages = Languages::all();

        $allowedAccounts = TaboolaAPI::$instance->getAllowedAccounts();

        $websites = Websites::all();
       // $customAudiences = TaboolaAPI::$instance->getAllCustomAudiences();

        $accountsList = [];
        foreach ($allowedAccounts->results as $allowedAccount) {
            if(isset($accountsIdToSlug[$allowedAccount->id])) {
                $accountsList[$allowedAccount->id] = [
                    "name" => $allowedAccount->name
                ];
            }
        }

        return view('admin.campaign.index', [
                'geoList' => $geoList,
                "accountsList" => $accountsList,
                "languages" => $languages,
                "websites"  => $websites,
                "devices"  => $devices
            ]
        );
    }

    public function checkImageProportions($uploaded_file_obj){

        $proportions_array = [
            1 => [
                'width'     => 16,
                'height'    => 9
            ],
            2 => [
                'width'     => 4,
                'height'    => 3
            ],
            3 => [
                'width'     => 1,
                'height'    => 1
            ]
        ];

        $image_sizes = getimagesize($uploaded_file_obj->getPathName());
        foreach($proportions_array as $proportion){
            //dump($image_sizes);
            $width_multiplier = round($image_sizes[0]/$proportion['width'], 0);
            $height_multiplier = round($image_sizes[1]/$proportion['height'], 0);
            //dump([$width_multiplier, $height_multiplier]);
            if($width_multiplier != $height_multiplier){
                //dump("not passed");
            } else {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function checkImageDimensions($uploaded_file_obj){
        $width = 1200;
        $height = 800;

        $image_sizes = getimagesize($uploaded_file_obj->getPathName());
        if($image_sizes[0] < $width || $image_sizes[1] < $height){
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function checkImageSize($uploaded_file_obj){

        $max_allowed_size = 1024*1024*2.5;

        //dump([$max_allowed_size, $uploaded_file_obj->getSize()]);
        if($max_allowed_size > $uploaded_file_obj->getSize()){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function preview(Request $request){

        $ads_images = $request->file('images');
        $ads_images_links = $request->post("images_links");
        $ads_titles = $request->post("ads_titles");
        $ads_url = $request->post("ads_url");
        $lang = Languages::where('id', $request->post("language"))->first();

        $ads_combinations = [];
        if(!empty($ads_images) || !empty($ads_images_links)) {
            if(!empty($ads_images)) {
                $ads_validation = [];
                foreach ($ads_images as $key => $image) {
                    $ads_validation[$key]['size'] = $this->checkImageSize($image);
                    $ads_validation[$key]['dimensions'] = $this->checkImageDimensions($image);
                    $ads_validation[$key]['proportions'] = $this->checkImageProportions($image);
                }
            }
            $ads_combinations = CampaignCreator::$instance->GenerateAdsCombinations($ads_images, $ads_images_links, $ads_titles, $ads_url, $lang->ad_title_prefix_outbrain);
        }

        if($request->post("sandbox")){
            $sandbox = TRUE;
        } else {
            $sandbox = FALSE;
        }

        $name = $request->post("name");
        $devices = $request->post("devices");
        $geos = $request->post("geos");
        $ad_platforms_taboola = $request->post("ad_platforms_taboola");
        $ad_platforms_outbrain = $request->post("ad_platforms_outbrain");
        //$accounts = $request->post("accounts");
        $accounts["taboola"] = $request->post("select_taboola_accounts");
        $accounts["outbrain"] = $request->post("select_outbrain_accounts");
        $audience = [];
        $site_obj = Websites::find($request->post("website"));

        if($request->post("audience"))    $audience = $request->post("audience");

        if($name) {
            $site = $site_obj->slug;

            $adProviders = [];
            $selectedDevices = $devices;
            if($ad_platforms_taboola) {
                $adProviders[] = "taboola";
            }
            if($ad_platforms_outbrain) {
                $adProviders[] = "outbrain";
            }

            $campaigns = CampaignCreator::$instance->GenerateCampaigns($site, $accounts, $name, $selectedDevices, $geos, $sandbox, $audience, "", [], $adProviders);

            $collection = collect($campaigns);
            $sorted = $collection->sortBy([
                function ($a, $b) { return $a->adProvider <=> $b->adProvider; },
            ]);



            echo "Name of campaigns (".count($campaigns)."):</br>";
            $account_id = "";
            foreach ($sorted->all() as $campaign) {

                if($account_id != $campaign->account_id){
                    $account_name = Accounts::where('api_account_id', $campaign->account_id)->first()->name;
                    $account_id = $campaign->account_id;
                    echo "<span><b>".$account_name."</b></span></br>";
                }

                if($campaign->getCampainCPC()){
                    if($campaign->sandbox){
                        if(($campaign->adProvider == 'taboola' && $campaign->account_id != $campaign->getSandboxAccount('taboola')) || ($campaign->adProvider == 'outbrain' && $campaign->account_id != $campaign->getSandboxAccount('outbrain'))){
                            echo "<span  style='color: #FF0000'>[".$campaign->adProvider."] ".$campaign->getFullName()." - CPC: ".$campaign->getCampainCPC()." can't be created on not sandbox account</span></br>";
                        } else {
                            echo "<span>[".$campaign->adProvider."] ".$campaign->getFullName()." - CPC: ".$campaign->getCampainCPC()." ".($campaign->realcpc ? "(".$campaign->realcpc.")":"")."</span></br>";
                        }
                    } else {
                        echo "<span>[".$campaign->adProvider."] ".$campaign->getFullName()." - CPC: ".$campaign->getCampainCPC()." ".($campaign->realcpc ? "(".$campaign->realcpc.")":"")."</span></br>";
                    }
                } else {
                    echo "<span style='color: #FF0000'>[".$campaign->adProvider."] ".$campaign->getFullName()." - CPC not found campaign can't be created</span></br>";
                }
            }

            //dump($ads_validation);

            $titles_number = count($ads_titles);
            $counter = 0;

            echo "<br/>Ads (".count($ads_combinations)."):</br>";
            if(count($ads_combinations) > 0) {
                //echo '<span> Outbrain Title prefix: <b>' . $ads_combinations[0]->prefix . '</b></span><br/>';
                foreach ($ads_combinations as $key => $ad) {
                    if (!empty($ad->image_content)) {
                        echo '<span><img src="data:' . $ad->image_content_type . ';base64,' . base64_encode($ad->image_content) . '" width="250"/>';
                        $taboola_ad_title_length = strlen($ad->title);
                        $outbrain_ad_title_length = strlen($ad->prefix.' '.$ad->title);
                        if($taboola_ad_title_length > 90) {
                            echo '<p style="color:#FF0000">Taboola title: ' . $ad->title . '(length: ' . $taboola_ad_title_length . ')</p>';
                        } else {
                            echo '<p>Taboola title: ' . $ad->title . '(length: ' . $taboola_ad_title_length . ')</p>';
                        }
                        if($outbrain_ad_title_length > 90){
                            echo '<p style="color:#FF0000">Outbrain title: '.$ad->prefix.' '.$ad->title.'(length: '.$outbrain_ad_title_length.')</p></span><br/>';
                        } else {
                            echo '<p>Outbrain title: '.$ad->prefix.' '.$ad->title.'(length: '.$outbrain_ad_title_length.')</p></span><br/>';
                        }

                        if($key%$titles_number == $titles_number-1){
                            if(!$ads_validation[$counter]['size']){
                                echo '<span>Image should be less then 2,5 Mb</span><br/>';
                            }
                            if(!$ads_validation[$counter]['proportions']){
                                echo '
                                <span>Image not fit recommended proportions.<br/>
                                Recomended proportions for Tabula:<br/>
                                Proportions: 16:9, 4:3, 1:1<br/>
                                </span><br/>';
                            }
                            if(!$ads_validation[$counter]['dimensions']){
                                echo '
                                <span>Image not fit recommended dimensions.<br/>
                                Minimal dimension 600x400 px.<br/>
                                Recomended dimensions for 1200x800 px and more. <br/>
                                </span><br/>';
                            }

                            $counter++;
                        }
                    } else {
                        echo '<span><img src="'.$ad->image_url.'" width="250"/><br/>';
                        echo 'Taboola title: '.$ad->title.'<br/>';
                        echo 'Outbrain title: '.$ad->prefix.' '.$ad->title.'<br/></span><br/>';
                        if($key%$titles_number == $titles_number-1) {
                            $counter++;
                        }
                    }
                }
            }
        }

    }

    public function generate(Request $request){
        $name = $request->post("name");
        $devices = $request->post("devices");
        $geos = $request->post("geos");
        $accounts["taboola"] = $request->post("select_taboola_accounts");
        $accounts["outbrain"] = $request->post("select_outbrain_accounts");
        $ad_platforms_taboola = $request->post("ad_platforms_taboola");
        $ad_platforms_outbrain = $request->post("ad_platforms_outbrain");
        $audience = [];

        if($request->post("sandbox")){
            $sandbox = TRUE;
        } else {
            $sandbox = FALSE;
        }

        $site_obj = Websites::find($request->post("website"));
        $branding_text = $site_obj->name;//$request->post("branding_text");

        if ($request->hasFile('images')) {
            $images      = $request->file('images');
            foreach ($images as $image) {
                $path = $image->store('/images/tmp');
            }
        }
        if($request->post("audience"))    $audience = $request->post("audience");

        if($name) {
            $site = $site_obj->slug;

            $selectedDevices = $devices;
            $adsImages = [];
            $adsTitles = [];
            $images_links = [];
            $adsUrl = "";
            if ($request->hasFile('images')) {
                $adsImages = $request->file('images');
//                foreach ($images as $image) {
//                    $path = $image->store('/images/tmp');
//                }
            }
            if($request->post("ads_titles"))    $adsTitles = $request->post("ads_titles");
            if($request->post("ads_url"))    $adsUrl = $request->post("ads_url");
            if($request->post("images_links"))      $adsImagesLinks = $request->post('images_links');
            if($ad_platforms_taboola) {
                $adProviders[] = "taboola";
            }
            if($ad_platforms_outbrain) {
                $adProviders[] = "outbrain";
            }

            $lang = Languages::where('id', $request->post("language"))->first();

            $campaigns = CampaignCreator::$instance->generate($site, $accounts, $name, $selectedDevices, $geos, $sandbox, $audience, $branding_text, $adsImages, $adsImagesLinks, $adsTitles, $adsUrl, [], $lang->ad_title_prefix_outbrain);
            $collection = collect($campaigns);
            $sorted = $collection->sortBy([
                function ($a, $b) { return $a->adProvider <=> $b->adProvider; },
            ]);

            echo "Name of campaigns (".count($campaigns)."):</br>";
            $campaignAdProvidersIDs = [];
            $campaignsThatNeedToBeSendToOutbrain = [];
            $account_id = "";
            foreach ($sorted->all() as $campaign) {

                if($account_id != $campaign->account_id){
                    $account_name = Accounts::where('api_account_id', $campaign->account_id)->first()->name;
                    $account_id = $campaign->account_id;
                    echo "<span><b>".$account_name."</b></span></br>";
                }

                if($campaign->getCampainCPC()){
                    if($campaign->realcpc) $campaignsThatNeedToBeSendToOutbrain[] = $campaign;
                    $campaignAdProvidersIDs[$campaign->adProvider][] = $campaign->campaignId;
                    echo "<span>[".$campaign->adProvider."] ".$campaign->getFullName()." - CPC: ".$campaign->getCampainCPC()." ".($campaign->realcpc ? "(".$campaign->realcpc.")":"")." - ID: ".$campaign->campaignId."</span></br>";
                } else {
                    echo "<span style='color: #FF0000'>[".$campaign->adProvider."] ".$campaign->getFullName()." - CPC not found campaign can't be created</span></br>";
                }
            }

            foreach ($campaignAdProvidersIDs as $AdProvider=> $campaignAdProviderIDs) {
                echo "</br> Campaign IDs on ".$AdProvider.": ".implode(", ", $campaignAdProviderIDs);
            }
            if(!empty($campaignsThatNeedToBeSendToOutbrain)) {
                echo "</br> </br> <b>This campaigns must be send to Outbrain to change CPC</b>";
                foreach ($campaignsThatNeedToBeSendToOutbrain as  $campaign) {
                    echo "</br> Campaign ID ".$campaign->campaignId." - must be CPC: ".$campaign->realcpc;
                }
            }

        }
    }

    public function getPrefixesLength(Request $request){
        $geos_array = $request->get("geos_slugs");
        $geos_ids = Geos::whereIn('slug', $geos_array)->get();
        $langs = Languages::all();

        $prefixes_array = [];

        foreach($geos_ids as $geo){
            foreach($langs as $lang){
                $lang_geos = explode(",", $lang->geos_list);
                if(in_array($geo->id, $lang_geos)){
                    $prefixes_array[] = $lang->ad_title_prefix_outbrain;
                }
            }
        }
        $prefixes_array = array_unique($prefixes_array);
        $min = 0;
        $max = 0;
        foreach($prefixes_array as $key => $item){
            $string_length = strlen($item);
            if($key == 0){
                $min = $string_length;
                $max = $string_length;
            } else {
                if ($string_length > $max) {
                    $max = $string_length;
                }
                if ($string_length < $min) {
                    $min = $string_length;
                }
            }
        }
        // adding 1 for count space in title between prefix and title
        return json_encode([$min+1, $max+1]);
    }

    public function getCustomAudience(Request $request){

        $account_id = $request->get("id");

        $allowedAccounts = TaboolaAPI::$instance->getAllowedAccounts();
        $account = null;

        foreach ($allowedAccounts->results as $allowedAccount) {
            if($allowedAccount->account_id == $account_id) {
                $account = $allowedAccount;
            }
        }

        if($account) {
            $customAudiences = TaboolaAPI::$instance->getAllCustomAudiences($account->account_id);
            $audiences = [];
            foreach($customAudiences->results as $audience){
                if($audience->status == 'ACTIVE') {
                    $audience_slug = preg_split('/ - /', $audience->display_name);
                    $audience_slug = ucfirst(preg_replace('/\s/', "", $audience_slug[0]));
                    $audiences[$audience->id . "|" . $audience_slug] = $audience->display_name;
                } else {

                }
            }

            return view('admin.campaign._audience', [
                    'audiencesList' => $audiences,
                    "accountId"     => $account->account_id,
                    "accountName"   => $account->name
                ]
            );
        } else {
            echo "";
        }
    }

    public function getCustomAudienceOutbrain(Request $request){

        $account_id = $request->get("id");

        $allowedAccounts = OutbrainAPI::$instance->getAccounts();
        $account = null;

        foreach ($allowedAccounts->marketers as $allowedAccount) {
            if($allowedAccount->id == $account_id) {
                $account = $allowedAccount;
            }
        }

        if($account) {
            $customAudiences = OutbrainAPI::$instance->getAccountAudiences($account->id);
            $audiences = [];
            foreach($customAudiences->segments as $segment){
                //dump($segment);
                $segment_slug = preg_replace('/_segment$/', "", $segment->name);
                $segment_slug = ucfirst(preg_replace('/\s/', "", $segment_slug));

                $audiences[$segment->id."|".$segment_slug] = $segment->name;
            }
            return view('admin.campaign._audience', [
                    'audiencesList' => $audiences,
                    "accountId"     => $account->id,
                    "accountName"   => $account->name
                ]
            );
        } else {
            echo "";
        }
    }

    public function generateCountriesPart(Request $request) {

        $geoList = Geos::all();

        if(!empty($request->get("language"))) {
            $language = Languages::find($request->get("language"));

            $geos_array = [];
            foreach($geoList as $item){
                $geos_array[$item->id] = $item->slug;
            }
            $selected_geos = explode(",", $language->geos_list);

            $re_lang_geos_array = [];
            foreach($selected_geos as $geo){
                /* IF added for support rows saved before with slug */
                if(!in_array($geo, $geos_array)){
                    $re_lang_geos_array[] = $geos_array[$geo];
                } else {
                    $re_lang_geos_array[] = $geo;
                }
            }

            $selected_geos = $re_lang_geos_array;
        } else {
            $selected_geos = [];
        }

        return view('admin.campaign.geoList', [
                'geoList' => $geoList,
                "selected" => $selected_geos
            ]
        );
    }

    public function campaignAccounts(Request $request){
        $accounts = '';

        if($request->get('taboola_checked') == 1 && $request->get('outbrain_checked') == 1){
            $accounts = Accounts::query()
                ->where('website_id', '=', $request->get('website_id'))
                ->whereIn('platform', ['taboola', 'outbrain'])
                ->get();
        } elseif($request->get('taboola_checked') == 1){
            $accounts = Accounts::query()
                ->where('website_id', '=', $request->get('website_id'))
                ->where('platform', '=', 'taboola')
                ->get();
        } elseif($request->get('outbrain_checked') == 1){
            $accounts = Accounts::query()
                ->where('website_id', '=', $request->get('website_id'))
                ->where('platform', '=', 'outbrain')
                ->get();
        }

        //dump($accounts);
        $geos = $request->get('geos');
        //dump($geos);
        $geos_rows = Geos::whereIn('slug', $geos)->get();//Geos::where('slug', 'IN', $geos)->get();
        //dump($geos_rows);
        $accounts_from_geos = [];
        foreach ($geos_rows as $geo_row){
            $accounts_ids = $geo_row->accounts()->allRelatedIds();
            foreach($accounts_ids as $item){
                if(!in_array($item, $accounts_from_geos)){
                    array_push($accounts_from_geos, $item);
                }
            }
            //dump($accounts_ids);
        }
        //dump($accounts_from_geos);



        $re_accounts = [];
        foreach($accounts as $key => $value){
            if(in_array($value->id, $accounts_from_geos)) {
                if ($value->platform == "taboola") {
                    $re_accounts['taboola'][$value->api_account_id] = $value->name;
                } else if ($value->platform == "outbrain") {
                    $re_accounts['outbrain'][$value->api_account_id] = $value->name;
                }
            }
        }

        //dd();

        return view('admin.campaign._accounts', [
                "display"   => '',
                "accounts"  => $re_accounts
            ]
        );
    }
}
