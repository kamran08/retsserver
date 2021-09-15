<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 360000000000); //3 minutes
ini_set('memory_limit', '-1');
date_default_timezone_set('America/New_York');


use Illuminate\Http\Request;
use App\ErrorStore;
use App\Picture;
use App\JsonData;
use App\Listing;
use App\Checker;
use App\UpdateChecker;
use App\MapMissingRequest;
use App\MapRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use File;
use DB;
use Image;
use DateTime;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Stmt\TryCatch;

class RetsController extends Controller
{
       
    // data formate method
    public function createNewListing($data, $type)
    {
        $ss = json_encode($data);
        
        if ($data['LM_Char10_11'] == 'House/Single Family') {
            $data['LM_Char10_11'] = 'House';
        } else if ($data['LM_Char10_11'] == 'Apartment/Condo') {
            $data['LM_Char10_11'] = 'Condo';
        } else if ($data['LM_Char10_11'] == 'Townhouse') {
            $data['LM_Char10_11'] = 'Townhouse';
        } else if ($data['LM_Char10_11'] == '1/2 Duplex') {
            $data['LM_Char10_11'] = 'Duplex';
        }

        $d = [
            'listingID' => isset($data['L_ListingID'])?$data['L_ListingID']:null,
            'class' => $type,
            'listingType' => isset($data['L_Type_'])? $data['L_Type_']:null,
            'completed'=>1,
            'listingArea' => isset($data['L_Area'])?$data['L_Area']:null,
            'listingAddress' => isset($data['L_Address'])?$data['L_Address']:null,
            'listingAddressDirection' => isset($data['L_AddressDirection'])? $data['L_AddressDirection']:null,
            'listingAddressStreet' => isset($data['L_AddressStreet'])?$data['L_AddressStreet']:null,
            'listingAddressUnit' => isset($data['L_AddressUnit'])?$data['L_AddressUnit']:null,
            'amenities' => isset($data['LFD_Amenities_25'])?$data['LFD_Amenities_25']:'null',
            'basementArea' => isset($data['LFD_BasementArea_6'])?$data['LFD_BasementArea_6']:null,
            'lotSizeLenth' => isset($data['LM_char30_28'])?$data['LM_char30_28']:null,
            'onInternet' => isset($data['LV_vow_address'])? $data['LV_vow_address']:null,
            'features' => isset($data['LFD_FeaturesIncluded_24'])?$data['LFD_FeaturesIncluded_24']:null,
            'fireplaces' => isset($data['LM_Int1_2'])?$data['LM_Int1_2']:null,
            'floorAreaTotal' => isset($data['LM_Dec_7'])?$data['LM_Dec_7']:null,
            'lotSizeWidthFeet' => isset($data['LM_Dec_8'])?$data['LM_Dec_8']:null,
            'lotSizeMeter' => isset($data['LM_Dec_9'])?$data['LM_Dec_9']:null,
            'internetRemarks' => isset($data['LR_remarks33'])?$data['LR_remarks33']:null,
            'listingDate' => isset($data['L_ListingDate'])? $data['L_ListingDate']:null,
            'updateDate' => isset($data['L_UpdateDate'])?$data['L_UpdateDate']:null,
            'addressNumber' => isset($data['L_AddressNumber'])?$data['L_AddressNumber']:null,
            'city' => isset($data['L_City'])? $data['L_City']:null,
            'subArea' => isset($data['LM_Char10_5'])? $data['LM_Char10_5']:null,
            'state' => isset($data['L_State'])? $data['L_State']:null,
            'zip' => isset($data['L_Zip'])?$data['L_Zip']:null,
            'askingPrice' => isset($data['L_AskingPrice'])? $data['L_AskingPrice']:null,
            'grossTaxes' => isset($data['LM_Dec_16'])?$data['LM_Dec_16']:null,
            'lotSizeArea' => isset($data['LM_Dec_12'])? $data['LM_Dec_12']:null,
            'lotSizeAreaSqMt' => isset($data['LM_Dec_13'])? $data['LM_Dec_13']:null,
            'lotSizeAreaSqFt' => isset($data['LM_Dec_11'])? $data['LM_Dec_11']:null,
            'displayId' => isset($data['L_DisplayId'])? $data['L_DisplayId']:null,
            'floorLevel' => isset($data['LM_Int1_1'])?$data['LM_Int1_1']:null,
            'pictureCount' => isset($data['L_PictureCount'])?$data['L_PictureCount']:null,
            'lastPhotoUpdate' => isset($data['L_Last_Photo_updt'])? $data['L_Last_Photo_updt']:null,
            'status' => isset($data['L_Status'])?$data['L_Status']:null,
            'houseType' => isset($data['LM_Char10_11'])?$data['LM_Char10_11']:null,
            // 'lat' => null,
            // 'lang' => null,
            'totalBedrooms' => isset($data['LM_Int1_4'])? $data['LM_Int1_4']:null,
            'totalRooms' => isset($data['LM_Int1_7'])? $data['LM_Int1_7']:null,
            'halfBaths' => isset($data['LM_Int1_17'])? $data['LM_Int1_17']:null,
            'fullBaths' =>isset( $data['LM_Int1_18'])? $data['LM_Int1_18']:null,
            'totalBaths' => isset($data['LM_Int1_19'])?$data['LM_Int1_19']:null,
            'age' => isset($data['LM_Int2_3'])? $data['LM_Int2_3']:null,
            'yearBuilt' => isset($data['LM_Int2_2'])? $data['LM_Int2_2']:null,
            'texPerYear' => isset($data['LM_Int2_5'])? $data['LM_Int2_5']:null,
            'unitsInDevelopment' =>isset( $data['LM_Int4_1'])? $data['LM_Int4_1']:null,
            'kitchens' => isset($data['LM_Int1_8'])?$data['LM_Int1_8']:null,
            'json_data' => $ss,
            'perSqrtPrice' => isset($data['L_PricePerSQFT'])?$data['L_PricePerSQFT']:null,
            'organizationName1' => isset($data['LO1_OrganizationName'])?$data['LO1_OrganizationName']:null,
            'organizationName2' => isset($data['LO2_OrganizationName'])?$data['LO2_OrganizationName']:null,
            'startaFee' => isset($data['LM_Dec_22'])?$data['LM_Dec_22']:null,
            'originalListPrice' => isset($data['L_OriginalPrice'])?$data['L_OriginalPrice']:null,
            'soldPrice' => isset($data['L_SoldPrice'])?$data['L_SoldPrice']:null,
            'previousPrice' => isset($data['LM_int4_40'])?$data['LM_int4_40']:null,
            'soldPricePerSqrt' => isset($data['LM_Dec_24'])?$data['LM_Dec_24']:null,
        ];
        return $d;
        // return Listing::create($d);
    }
    // end data format

    // Featch RD_1 Data

    public function featchRdData(){
        $idd = Checker::first();
        try {
            if ($idd && $idd['status1'] == 'Running') return 1;
            Checker::where('id', $idd['id'])->update(['status1' => 'Running']);
            // connection start
            
            $config = new \PHRETS\Configuration;
            $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
                ->setUsername('RETSARVING')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setRetsVersion('1.7.2');
            \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $rets = new \PHRETS\Session($config);
            $connect = $rets->Login();

            // end connecton

            $ofset = 0;
            if ($idd) {
                $ofset = $idd['lastId'];
            }
            // $results   = $rets->Search('Property',  'RD_1', "(L_Area=|1,2,3,4,5,7,8,9,10,12,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30),(L_Status=1_0,2_0,4_0,5_1,5_2),(LM_Char10_11=|HOUSE)", ['Limit'  =>  50, 'Offset' => $ofset]);
            $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)", ['Limit'  =>  100, 'Offset' => $ofset]);
            $alldata  = $results->toArray();
            $temp = [];
            foreach ($alldata as $key => $val) {
                $jsonV = '';

                if (!$jsonV || (isset($jsonV['listingID']) && $jsonV['listingID'] != $val['L_ListingID'])) {

                    $jsonV  = $this->createNewListing($val, 'RD_1');
                    array_push($temp, $jsonV);
                }
                $ofset++;
               
            }
            $l = Listing::insert($temp);
            if($l)
            Checker::where('id', $idd['id'])->update(['lastId' => $ofset, 'status1' => 'Stop']);
            return "successfully inserted data";
        } catch (\Exception $e) {
            Checker::where('id', $idd['id'])->update(['status1' => 'Fail']);
           \Log::info($e);
            return "fail";
        }
    }
    //End Featch RD_1 Data
    // Featch RA_2 Data

    public function featchRAData(){
        $idd = Checker::first();
        try {
            if ($idd && $idd['status2'] == 'Running') return 1;
            Checker::where('id', $idd['id'])->update(['status2' => 'Running']);
            // connection start
           
         
            $config = new \PHRETS\Configuration;
            $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
            ->setUsername('RETSARVING')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setRetsVersion('1.7.2');
            \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $rets = new \PHRETS\Session($config);
            $connect = $rets->Login();

            // end connecton

            $ofset = 0;
            if ($idd) {
                $ofset = $idd['lastId2'];
            }
            $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS)", ['Limit'  =>   100, 'Offset' => $ofset]);
            // $results   = $rets->Search('Property',  'RA_2', "(L_Area=|1,2,3,4,5,7,8,9,10,12,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30),(L_Status=1_0,2_0,4_0,5_1,5_2),(LM_Char10_11=|APTU,DUPXH,TWNHS)", ['Limit'  =>   50, 'Offset' => $ofset]);
            $alldata  = $results->toArray();
            $temp = [];
            foreach ($alldata as $key => $val) {
                $jsonV = '';

                if (!$jsonV || (isset($jsonV['listingID']) && $jsonV['listingID'] != $val['L_ListingID'])) {

                    $jsonV  = $this->createNewListing($val, 'RA_2');
                    array_push($temp, $jsonV);
                }
                $ofset++;
               
            }
            $l = Listing::insert($temp);
            if($l)
            Checker::where('id', $idd['id'])->update(['lastId2' => $ofset, 'status2' => 'Stop']);
            return "successfully inserted data";
        } catch (\Exception $e) {
            Checker::where('id', $idd['id'])->update(['status2' => 'Fail']);
            \Log::info($e);
            return "fail";
        }
    }
    //End Featch RA_2 Data
    // start location data 
    public function getLocation(){
        // return "check";
        \Log::info("method is calling...");
        $alldata = Listing::where('lat',null)->where('lang',null)->doesnthave('missed')
        ->select('id', 'listingID', 'lat','lang', 'listingAddress')->limit(100)->get();
        // return $alldata;
        $date =   date("Y-m-d");
        $mapreq = MapRequest::where('date', $date)->first();
        if($mapreq) {
            if($mapreq['counter'] >= 6000) return 1;
            
        }
        else{
            \Log::info("Nai");
            $mapreq = MapRequest::create([
                "counter" => 0,
                "date" => $date
            ]);
        }
        // return $alldata;
        foreach($alldata as $key => $d){
            \Log::info("alldata");
            if($mapreq['counter'] >=6000) return 1;
            if($d['listingAddress']){
                $d['listingAddress'] = trim($d['listingAddress'],"#");
            
                $client = new \GuzzleHttp\Client();
                $request ='';
                
                if($mapreq['counter'] >=0 && $mapreq['counter'] <=2000){
                    $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCZ1Qzl88y2a9m4sP9zLk8s4LRS78yFFdg&address=' . $d['listingAddress'].',ca')->getBody();
                }
                if($mapreq['counter'] >2000 && $mapreq['counter'] <=4000){
                        $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyChBdKomhhVm_TH4H4i-qjyvFpON9g3b48&address=' . $d['listingAddress'].',ca')->getBody();
                }
                if($mapreq['counter'] >4000 && $mapreq['counter'] <=6000){
                    $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAzhVjq0RixepWJyxO1CPnR-exUYpxRrTo&address=' . $d['listingAddress'].',ca')->getBody(); //sadek api
                }
                   
                    $json = json_decode($request);
                    $lat = null;
                    $lang = null;
                    if(sizeof($json->results)>0){
                    \Log::info("exicuted");
                        $lat = $json->results[0]->geometry->location->lat;
                        $lang = $json->results[0]->geometry->location->lng;
                    } 
                    else {
                    \Log::info("not exicuted");
                            $ob = [
                                'list_id' => $d['id'],
                                'listingID' => $d['listingID'],
                                'listingAddress' => $d['listingAddress']
                            ];
                            MapMissingRequest::create($ob);
                        }


                        DB::table('map_requests')->where('id', $mapreq['id'])->update([
                            'counter' => DB::raw('counter + 1')
                        ]);
                        $mapreq['counter']+=1;

                    $s = DB::table('listings')
                        ->where('id', $d['id'])
                        ->update([
                            'lat' => $lat,
                            'lang' => $lang,
                            'isSent' => 'sent',
                        ]);
                    
                    // $s = Listing::where('id', $d['id'])->where('lat', '!=', null)->first();
                    $s = Listing::where('id', $d['id'])->whereNotNull('lat')->first();
                    if($s){
                        try{
                            $l = json_decode(json_encode($s), true);
                        // $request2 = Http::post('https://youhome.cc/storeDataFromDataServer', $s);
                        // return 1;

                        $client2 = new \GuzzleHttp\Client();
                        $request2 = (string) $client2->post('https://m.youhome.cc/storeDataFromDataServer', ['form_params' => $l])->getBody();
                        // $json2 = json_decode($request2);

                    } catch (\Exception $e) {
                        $do = json_encode(["error"=>$e, "type"=>'not sent']);
                
                     ErrorStore::create(["data" => $do]);
                        \Log::info($e);
                        DB::table('listings')
                        ->where('id', $d['id'])
                        ->update([
                            'isSent' => 'not sent',
                        ]);
                        return "error";
                }

            }
            else{
                DB::table('listings')
                ->where('id', $d['id'])
                ->update([
                    'isSent' => 'not sent',
                ]);
            }
        }

        }
        return "success";
    } 
    //End location data 


    // start storeImages data 
    public function storeImages(){

            set_time_limit(2000000);
            $config = new \PHRETS\Configuration;
            $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
                ->setUsername('RETSARVING')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setRetsVersion('1.7.2');
            \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $rets = new \PHRETS\Session($config);
            $connect = $rets->Login();
        

        $alldata = Listing::where('completed','!=', 3)->where('isSent', 'sent')->select('id', 'listingID')->limit(100)->get();
        foreach($alldata as $key => $val){
            $objects = $rets->GetObject('Property', 'Photo', $val['listingID'], '*', 0);
            $data = [];
            $l =0;
                $img='';
            
            try{
            foreach ($objects as $ke => $photo) {
                $url = $photo->getContent();
                $name = time() . uniqid(rand()) . '.png';
                if($l==0){
                    $name1 = time() . uniqid(rand()) . '.webp';
                // Image::make('/uploads/' . $request->file('file'))->encode('webp', 50);
                        $image = Image::make($url)->encode('webp', 90)->encode('webp', 50);
                        $myFile = Storage::disk('spaces')->put($name1, $image);
                        Storage::disk('spaces')->setVisibility($name1, 'public');
                        $img = Storage::disk('spaces')->url($name1);
                }
                $l=2;
                $myFile = Storage::disk('spaces')->put($name, $url);
                Storage::disk('spaces')->setVisibility($name, 'public');
                $ll = Storage::disk('spaces')->url($name);
                array_push($data, $ll);
            }
            } catch (\Exception $e) {
                    $do = json_encode($val);
                
                     ErrorStore::create(["data" => $do]);
                }
            $data = json_encode($data);

            $s = DB::table('listings')
            ->where('id', $val['id'])
            ->update([
                'thumbnail' => $img,
                'images' => $data,
                'completed' => 3
            ]);
            $ob = [
                'listingID' =>  $val['listingID'],
                'thumbnail' => $img,
                'images' => $data
            ];
                try{
                    // $request2 = Http::post('https://youhome.cc/storeImageDataFromDataServer', $s);
                    // return 1;
                    $client2 = new \GuzzleHttp\Client();
                    $request2 = (string) $client2->post('https://m.youhome.cc/storeImageDataFromDataServer', ['form_params' => $ob])->getBody();
                    $json2 = json_decode($request2);
                } catch (\Exception $e) {
                    \Log::info($e);
                    return false;
                }

            
        

        }
        return "success";
    } 
    //End storeImages data
    public function checkForUpdatedData(){
        $start = microtime(true);

        $idd = UpdateChecker::first();
        $checklisting = Listing::where('class', 'RD_1')->count();
        if ($idd['lastId'] >= $checklisting) {
            UpdateChecker::where('id', $idd['id'])->update(['lastId'=>0]);
           \Log::info("stop lstid 0");
            return 1;
        }
        if ($idd && $idd['status1'] == 'Running') {
            \Log::info("stop running 0");
            return 1;
        }
        UpdateChecker::where('id', $idd['id'])->update(['status1' => 'Running']);
        try{
            set_time_limit(2000000);
            $config = new \PHRETS\Configuration;
            // $config = \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
                ->setUsername('RETSARVING')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setRetsVersion('1.7.2');
            \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $rets = new \PHRETS\Session($config);
            $connect = $rets->Login();
            $resource = 'Property';
            $photo_resource_type = 'Property';
            $ofset = 0;
            if($idd){
                $ofset = $idd['lastId'];
            }
            $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)", ['Limit'  =>  1000, 'Offset' => $ofset]);
            $alldata  = $results->toArray();
            $listingIds = [];
            foreach ($alldata as $value ) {
                array_push($listingIds,$value['L_ListingID']);
                $ofset++;
            }
            $listingData  = Listing::whereIn('listingID',$listingIds)->select('id','listingID','updateDate','lastPhotoUpdate')->get(); ;
            
            $updateArray=[];
            foreach ($listingData as $value ) {
                $index = -1;
                $serverValueLength = sizeof($alldata);
                if($serverValueLength > 0){
                    for($i=0;$i<$serverValueLength;$i++){
                        if($value->listingID == $alldata[$i]['L_ListingID']){
                            $db_date = strtotime($value->updateDate);
                            $rets_date = strtotime($alldata[$i]['L_UpdateDate']);
                            if($db_date < $rets_date){
                                $index = $i;
                                $ob = [
                                    'L_ListingID'=>$value->listingID,
                                    'db_value'=>$value->updateDate,
                                    'rets_value'=>$alldata[$i]['L_UpdateDate'],
                                ];
                                $this->updateListingChanges($alldata[$i],$value);
                                array_push($updateArray,$ob);
                            }
                        }
                    }
                }
                $ofset++;

            }
            $check = UpdateChecker::where('id', $idd['id'])->update(['lastId' => $ofset, 'status1' => 'Stop']);
            
            \Log::info("end");
            $time_elapsed_secs = microtime(true) - $start;
            \Log::info($time_elapsed_secs);

            return response()->json([
                'success' => $check,
                'listingData' => $listingData,
                'updateArray' => $updateArray,
            ], 200);
        }
        catch(\Exception $e){
            \Log::info("end in catch");
            $time_elapsed_secs = microtime(true) - $start;
            // $interval = strtotime($datetime1->getTimestamp()) - strtotime($datetime2->getTimestamp());
            // $interval =   strtotime($datetime1->getTimestamp()) - strtotime($datetime2->getTimestamp());
            \Log::info($time_elapsed_secs);
            return $e;
        }
    }
    
    public function checkForUpdatedData2(){
        $idd = UpdateChecker::first();
        $checklisting = Listing::where('class', 'RA_2')->count();
        if ($idd['lastId2']>=$checklisting) {
            UpdateChecker::where('id', $idd['id'])->update(['lastId2' => 0]);
            return 1;
        }
        if ($idd && $idd['status2'] == 'Running') return 1;
        UpdateChecker::where('id', $idd['id'])->update(['status2' => 'Running']);
        try{
            set_time_limit(2000000);
            $config = new \PHRETS\Configuration;
            // $config = \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
                ->setUsername('RETSARVING')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setPassword('wjq6PJqUA45EGU8')
                ->setRetsVersion('1.7.2');
            \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
            $rets = new \PHRETS\Session($config);
            $connect = $rets->Login();
            $resource = 'Property';
            $photo_resource_type = 'Property';
            $ofset = 0;
            if($idd){
                $ofset = $idd['lastId2'];
            }
            $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)", ['Limit'  =>  100, 'Offset' => $ofset]);
            $alldata  = $results->toArray();
            $listingIds = [];
            foreach ($alldata as $value ) {
                array_push($listingIds,$value['L_ListingID']);
                $ofset++;
            }
            $listingData  = Listing::whereIn('listingID',$listingIds)->select('id','listingID','updateDate','lastPhotoUpdate')->get(); ;
            
            $updateArray=[];
            foreach ($listingData as $value ) {
                $index = -1;
                $serverValueLength = sizeof($alldata);
                if($serverValueLength > 0){
                    for($i=0;$i<$serverValueLength;$i++){
                        if($value->listingID == $alldata[$i]['L_ListingID']){
                            $db_date = strtotime($value->updateDate);
                            $rets_date = strtotime($alldata[$i]['L_UpdateDate']);
                            if($db_date < $rets_date){
                                $index = $i;
                                $ob = [
                                    'L_ListingID'=>$value->listingID,
                                    'db_value'=>$value->updateDate,
                                    'rets_value'=>$alldata[$i]['L_UpdateDate'],
                                ];
                                $this->updateListingChanges($alldata[$i],$value);
                                array_push($updateArray,$ob);
                            }
                        }
                    }
                }
                $ofset++;

            }
            $check = UpdateChecker::where('id', $idd['id'])->update(['lastId2' => $ofset, 'status2' => 'Stop']);
            return response()->json([
                'success' => $check,
                'listingData' => $listingData,
                'updateArray' => $updateArray,
            ], 200);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public function updateListingChanges($retsData,$serverData){
        $status = 3;
        $db_date = strtotime($serverData->lastPhotoUpdate);
        $rets_date = strtotime($retsData['L_Last_Photo_updt']);
        if($db_date < $rets_date){
            $status = 2;
        }
        $ss = json_encode($retsData);
        $dd = [
            'isSent'=>'not sent',
            'completed'=>$status,
            'json_data' => $ss,
        ];
        if($retsData['L_Type_']) $dd['listingType'] = $retsData['L_Type_'];
        if($retsData['L_Area']) $dd['listingArea'] = $retsData['L_Area'];
        if($retsData['L_Address']) $dd['listingAddress'] = $retsData['L_Address'];
        if($retsData['L_AddressDirection']) $dd['listingAddressDirection'] = $retsData['L_AddressDirection'];
        if($retsData['L_AddressStreet']) $dd['listingAddressStreet'] = $retsData['L_AddressStreet'];
        if($retsData['L_AddressUnit']) $dd['listingAddressUnit'] = $retsData['L_AddressUnit'];
        if($retsData['LFD_Amenities_25']) $dd['amenities'] = $retsData['LFD_Amenities_25'];
        if($retsData['LFD_BasementArea_6']) $dd['basementArea'] = $retsData['LFD_BasementArea_6'];
        if($retsData['LM_char30_28']) $dd['lotSizeLenth'] = $retsData['LM_char30_28'];
        if($retsData['LV_vow_address']) $dd['onInternet'] = $retsData['LV_vow_address'];
        if($retsData['LFD_FeaturesIncluded_24']) $dd['features'] = $retsData['LFD_FeaturesIncluded_24'];
        if($retsData['LM_Int1_2']) $dd['fireplaces'] = $retsData['LM_Int1_2'];
        if($retsData['LM_Dec_7']) $dd['floorAreaTotal'] = $retsData['LM_Dec_7'];
        if($retsData['LM_Dec_8']) $dd['lotSizeWidthFeet'] = $retsData['LM_Dec_8'];
        if($retsData['LM_Dec_9']) $dd['lotSizeMeter'] = $retsData['LM_Dec_9'];
        if($retsData['LR_remarks33']) $dd['internetRemarks'] = $retsData['LR_remarks33'];
        if($retsData['L_ListingDate']) $dd['listingDate'] = $retsData['L_ListingDate'];
        if($retsData['L_UpdateDate']) $dd['updateDate'] = $retsData['L_UpdateDate'];
        if($retsData['L_AddressNumber']) $dd['addressNumber'] = $retsData['L_AddressNumber'];
        if($retsData['L_City']) $dd['city'] = $retsData['L_City'];
        if($retsData['LM_Char10_5']) $dd['subArea'] = $retsData['LM_Char10_5'];
        if($retsData['L_State']) $dd['state'] = $retsData['L_State'];
        if($retsData['L_Zip']) $dd['zip'] = $retsData['L_Zip'];
        if($retsData['L_AskingPrice']) $dd['askingPrice'] = $retsData['L_AskingPrice'];
        if($retsData['LM_Dec_16']) $dd['grossTaxes'] = $retsData['LM_Dec_16'];
        if($retsData['LM_Dec_12']) $dd['lotSizeArea'] = $retsData['LM_Dec_12'];
        if($retsData['LM_Dec_13']) $dd['lotSizeAreaSqMt'] = $retsData['LM_Dec_13'];
        if($retsData['LM_Dec_11']) $dd['lotSizeAreaSqFt'] = $retsData['LM_Dec_11'];
        if($retsData['L_DisplayId']) $dd['displayId'] = $retsData['L_DisplayId'];
        if($retsData['LM_Int1_1']) $dd['floorLevel'] = $retsData['LM_Int1_1'];
        if($retsData['L_PictureCount']) $dd['pictureCount'] = $retsData['L_PictureCount'];
        if($retsData['L_Last_Photo_updt']) $dd['lastPhotoUpdate'] = $retsData['L_Last_Photo_updt'];
        if($retsData['LM_Char10_11']) $dd['houseType'] = $retsData['LM_Char10_11'];
        if($retsData['L_Status']) $dd['status'] = $retsData['L_Status'];
        if($retsData['LM_Int1_4']) $dd['totalBedrooms'] = $retsData['LM_Int1_4'];
        if($retsData['LM_Int1_7']) $dd['totalRooms'] = $retsData['LM_Int1_7'];
        if($retsData['LM_Int1_17']) $dd['halfBaths'] = $retsData['LM_Int1_17'];
        if($retsData['LM_Int1_18']) $dd['fullBaths'] = $retsData['LM_Int1_18'];
        if($retsData['LM_Int1_19']) $dd['totalBaths'] = $retsData['LM_Int1_19'];
        if($retsData['LM_Int2_3']) $dd['age'] = $retsData['LM_Int2_3'];
        if($retsData['LM_Int2_2']) $dd['yearBuilt'] = $retsData['LM_Int2_2'];
        if($retsData['LM_Int2_5']) $dd['texPerYear'] = $retsData['LM_Int2_5'];
        if($retsData['LM_Int4_1']) $dd['unitsInDevelopment'] = $retsData['LM_Int4_1'];
        if($retsData['LM_Int1_8']) $dd['kitchens'] = $retsData['LM_Int1_8'];
        if($retsData['L_PricePerSQFT']) $dd['perSqrtPrice'] = $retsData['L_PricePerSQFT'];
        if($retsData['LO1_OrganizationName']) $dd['organizationName1'] = $retsData['LO1_OrganizationName'];
        if($retsData['LO2_OrganizationName']) $dd['organizationName2'] = $retsData['LO2_OrganizationName'];
        if($retsData['LM_Dec_22']) $dd['startaFee'] = $retsData['LM_Dec_22'];
        if($retsData['L_OriginalPrice']) $dd['originalListPrice'] = $retsData['L_OriginalPrice'];
        if($retsData['L_SoldPrice']) $dd['soldPrice'] = $retsData['L_SoldPrice'];
        if($retsData['LM_int4_40']) $dd['previousPrice'] = $retsData['LM_int4_40'];
        if($retsData['LM_Dec_24']) $dd['soldPricePerSqrt'] = $retsData['LM_Dec_24'];
        

        Listing::where('listingID',$serverData['listingID'])->update($dd);
        try {
            $l = json_decode(json_encode($dd), true);

            $client2 = new \GuzzleHttp\Client();
            $request2 = (string) $client2->post('https://m.youhome.cc/storeDataFromDataServer', ['form_params' => $l])->getBody();

        } catch (\Exception $e) {
            \Log::info($e);
            return "error";
        }


    }

    public function checkDifferent(){
         $start = microtime(true);
        sleep(2);
        
       
        return $time_elapsed_secs = microtime(true) - $start;
        // return $interval;
  
    }

    public function testUpdateCheck(){
        // return 200;
        // $d = Listing::select('updateDate')->orderBy('updateDate','desc')->first();
        // $now = new \DateTime();
        // $date = new DateTime($d['updateDate']);
        // $preDate= $date->format('Y-m-d\TH:i:s');
        // $nowDate =$now->format('Y-m-d\TH:i:s');

            // return "hello";"(L_UpdateDate='".$nowDate."-".$preDate.")"
               set_time_limit(2000000);
                $config = new \PHRETS\Configuration;
                $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
                    ->setUsername('RETSARVING')
                    ->setPassword('wjq6PJqUA45EGU8')
                    ->setRetsVersion('1.7.2');
                \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
                $rets = new \PHRETS\Session($config);
                $connect = $rets->Login();
                $resource = 'Property';
                // $ddd =$rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)", ['Limit'  => 1]);
                // $alldata= $ddd->toArray();
                // $results   = $rets->Search('Property',  'RD_1', "(L_UpdateDate=2021-09-06T00:00:00-2021-09-07T00:00:00)",['limit'=>5]);
                // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$nowDate."-".$preDate.")",['limit'=>5]);//,(L_UpdateDate=".$nowDate."-".$preDate.")
                // $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$nowDate."-".$preDate.")",['limit'=>5]);//,(L_UpdateDate=".$nowDate."-".$preDate.")
                // $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)",['limit'=>5]);//,(L_UpdateDate=".$nowDate."-".$preDate.")
                $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=2021-04-06T00:00:00-2021-09-12T00:00:00)",['limit'=>5]);//,(L_UpdateDate=".$nowDate."-".$preDate.")
                
                $alldata1  = $results->toArray();
                // $siz = sizeof($alldata);
                $alldata=  $results->getTotalResultsCount();
                \Log::info("Test check1");
                \Log::info($alldata);
                return $alldata1;
        }
        

}
