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

class RetsController extends Controller
{
    // data formate method
    public function createNewListing($data, $type)
    {
        \Log::info($data);
        return 1;
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
            'listingID' => $data['L_ListingID'],
            'class' => $type,
            'listingType' => $data['L_Type_'],
            'completed'=>1,
            'listingArea' => $data['L_Area'],
            'listingAddress' => $data['L_Address'],
            'listingAddressDirection' => $data['L_AddressDirection'],
            'listingAddressStreet' => $data['L_AddressStreet'],
            'listingAddressUnit' => $data['L_AddressUnit'],
            'amenities' => $data['LFD_Amenities_25'],
            'basementArea' => $data['LFD_BasementArea_6'],
            'lotSizeLenth' => $data['LM_char30_28'],
            'onInternet' => $data['LV_vow_address'],
            'features' => $data['LFD_FeaturesIncluded_24'],
            'fireplaces' => $data['LM_Int1_2'],
            'floorAreaTotal' => $data['LM_Dec_7'],
            'lotSizeWidthFeet' => $data['LM_Dec_8'],
            'lotSizeMeter' => $data['LM_Dec_9'],
            'internetRemarks' => $data['LR_remarks33'],
            'listingDate' => $data['L_ListingDate'],
            'updateDate' => $data['L_UpdateDate'],
            'addressNumber' => $data['L_AddressNumber'],
            'city' => $data['L_City'],
            'subArea' => $data['LM_Char10_5'],
            'state' => $data['L_State'],
            'zip' => $data['L_Zip'],
            'askingPrice' => $data['L_AskingPrice'],
            'grossTaxes' => $data['LM_Dec_16'],
            'lotSizeArea' => $data['LM_Dec_12'],
            'lotSizeAreaSqMt' => $data['LM_Dec_13'],
            'lotSizeAreaSqFt' => $data['LM_Dec_11'],
            'displayId' => $data['L_DisplayId'],
            'floorLevel' => $data['LM_Int1_1'],
            'pictureCount' => $data['L_PictureCount'],
            'lastPhotoUpdate' => $data['L_Last_Photo_updt'],
            'status' => $data['L_Status'],
            'houseType' => $data['LM_Char10_11'],
            // 'lat' => null,
            // 'lang' => null,
            'totalBedrooms' => $data['LM_Int1_4'],
            'totalRooms' => $data['LM_Int1_7'],
            'halfBaths' => $data['LM_Int1_17'],
            'fullBaths' => $data['LM_Int1_18'],
            'totalBaths' => $data['LM_Int1_19'],
            'age' => $data['LM_Int2_3'],
            'yearBuilt' => $data['LM_Int2_2'],
            'texPerYear' => $data['LM_Int2_5'],
            'unitsInDevelopment' => $data['LM_Int4_1'],
            'kitchens' => $data['LM_Int1_8'],
            'json_data' => $ss
        ];
        return $d;
        // return Listing::create($d);
    }
    // end data format

    // Featch RD_1 Data

    public function featchRdData(){
        try {
            $idd = Checker::first();
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
            return "fail";
        }
    }
    //End Featch RD_1 Data
    // Featch RA_2 Data

    public function featchRAData(){
        try {
            $idd = Checker::first();
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
            return "fail";
        }
    }
    //End Featch RA_2 Data
    // start location data 
        public function getLocation(){
            $alldata = Listing::where('lat',null)->orWhere('lang',null)->select('id', 'lat','lang', 'listingAddress')->limit(100)->get();
            // return $alldata;
            foreach($alldata as $key => $d){
                $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $d['listingAddress'])->getBody();
                $json = json_decode($request);
                $lat = $json->results[0]->geometry->location->lat;
                $lang = $json->results[0]->geometry->location->lng;
                $s = DB::table('listings')
                    ->where('id', $d['id'])
                    ->update([
                        'lat' => $lat,
                        'lang' => $lang,
                        'completed' => DB::raw('completed + 1'),
                    ]);
                $s = DB::table('listings')
                ->where('id', $d['id'])->where('completed',3)->first();
                if($s){
                    $client2 = new \GuzzleHttp\Client();
                    $request2 = (string) $client2->post('https://youhome.cc/storeDataFromDataServer',$s)->getBody();
                    $json2 = json_decode($request2);

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
            


            $alldata = Listing::select('id', 'listingID')->limit(100)->get();
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
                    'completed' => DB::raw('completed + 1'),
                    'thumbnail' => $img,
                    'images' => $data
                ]);
                // $s = DB::table('listings')
                // ->where('id', $val['id'])->select('id','completed')->first();
                $s = DB::table('listings')
                ->where('id', $val['id'])->where('completed', 3)->first();
                if($s){
                    $client2 = new \GuzzleHttp\Client();
                    $request2 = (string) $client2->post('https://youhome.cc/storeDataFromDataServer', $s)->getBody();
                    $json2 = json_decode($request2);

                }
            

            }
            return "success";
        } 
    //End storeImages data 

}
