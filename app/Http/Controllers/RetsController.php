<?php

namespace App\Http\Controllers;

date_default_timezone_set('America/New_York');

use Illuminate\Http\Request;
use App\Picture;
use App\JsonData;
use App\Listing;
use App\Checker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use File;
use DB;
use Image;
use Illuminate\Support\Facades\Http;

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
            'json_data' => $ss
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

            // end connecton

            $ofset = 0;
            if ($idd) {
                $ofset = $idd['lastId'];
            }
            $results   = $rets->Search('Property',  'RD_1', "(L_Area=|1,2,3,4,5,7,8,9,10,12,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30),(L_Status=1_0,2_0,4_0,5_1,5_2),(LM_Char10_11=|HOUSE)", ['Limit'  =>   1, 'Offset' => $ofset]);
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
            Checker::where('id', $idd['id'])->update(['lastId' => $ofset, 'status1' => 'Fail']);
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

            // end connecton

            $ofset = 0;
            if ($idd) {
                $ofset = $idd['lastId2'];
            }
            $results   = $rets->Search('Property',  'RA_2', "(L_Area=|1,2,3,4,5,7,8,9,10,12,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30),(L_Status=1_0,2_0,4_0,5_1,5_2),(LM_Char10_11=|APTU,DUPXH,TWNHS)", ['Limit'  =>   1, 'Offset' => $ofset]);
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
            Checker::where('id', $idd['id'])->update(['lastId2' => $ofset, 'status2' => 'Fail']);
            \Log::info($e);
            return "fail";
        }
    }
    //End Featch RA_2 Data
    // start location data 
        public function getLocation(){
            $alldata = Listing::where('lat',null)->orWhere('lang',null)->select('id', 'lat','lang', 'listingAddress')->limit(100)->get();
          
            
            foreach($alldata as $key => $d){
           
                $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $d['listingAddress'].',canada')->getBody();
                $json = json_decode($request);
                $lat = null;
                $lang = null;
                if(sizeof($json->results)>0){
                    $lat = $json->results[0]->geometry->location->lat;
                    $lang = $json->results[0]->geometry->location->lng;
                }         
                $s = DB::table('listings')
                    ->where('id', $d['id'])
                    ->update([
                        'lat' => $lat,
                        'lang' => $lang,
                        'completed' => DB::raw('completed + 1'),
                    ]);
                $s = DB::table('listings')
                ->where('id', $d['id'])->where('lat', '!=', null)->orWhere('lang', '!=', null)->first();
                if($s){
                    try{
                        \Log::info("now sending data from main server");
                    // $request2 = Http::post('https://youhome.cc/storeDataFromDataServer', $s);
                    // return 1;

                    $client2 = new \GuzzleHttp\Client();
                    $request2 = (string) $client2->post('https://youhome.cc/storeDataFromDataServer', ['form_params' => $s])->getBody();
                    // $json2 = json_decode($request2);
                     } catch (\Exception $e) {
                         \Log::info($e);
                         return "error";

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
            
  
            $alldata = Listing::where('completed','!=', 3)->select('id', 'listingID')->limit(100)->get();
            foreach($alldata as $key => $val){
                $objects = $rets->GetObject('Property', 'Photo', $val['listingID'], '*', 0);
                $data = [];
                $l =0;
                 $img='';
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
                    $myFile = Storage::disk('spaces')->put($name, $url);
                    Storage::disk('spaces')->setVisibility($name, 'public');
                    $ll = Storage::disk('spaces')->url($name);
                    array_push($data, $ll);
                }
                $data = json_encode($data);

                $s = DB::table('listings')
                ->where('id', $val['id'])
                ->update([
                    'thumbnail' => $img,
                    'images' => $data,
                    'completed' => DB::raw('completed + 1')
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
                        $request2 = (string) $client2->post('https://youhome.cc/storeImageDataFromDataServer', ['form_params' => $ob])->getBody();
                        $json2 = json_decode($request2);
                    } catch (\Exception $e) {
                        \Log::info($e);
                        return false;
                    }

                
            

            }
            return "success";
        } 
    //End storeImages data 

}
