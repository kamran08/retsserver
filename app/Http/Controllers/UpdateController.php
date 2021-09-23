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
use App\NewUpdate;
use App\NewUpdateCheker;
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
use Carbon\Carbon;
class UpdateController extends Controller
{
    //
    public function updateRa2Data(){
        \Log::info("update RA_2 starting...");
        $now = new \DateTime();
        $start =  $now->format('Y-m-d\TH:i:s');
        $finale =  date_sub($now, new \DateInterval("PT5M"));
        $end =  $finale->format('Y-m-d\TH:i:s');
    
         $check = NewUpdateCheker::first();
       
         if ($check && $check['radata_status'] == 'Running') {
            \Log::info("update RA_2 running....");
            return 1;
        }
        NewUpdateCheker::where('id', $check['id'])->update(['radata_status' => 'Running']);
        set_time_limit(2000000);
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
            ->setUsername('RETSARVING')
            ->setPassword('wjq6PJqUA45EGU8')
            ->setRetsVersion('1.7.2');
        \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
        $rets = new \PHRETS\Session($config);
        $connect = $rets->Login();
      
        // try {   

        // $ofset=$check['ra_2count']; 
        \Log::info("update RA_2 featching..."); 
        $results  = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$end."-".$start.")",['select'=>'L_ListingID']);//,(L_UpdateDate=".$nowDate."-".$preDate.")
        // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$nowDate."-".$preDate.")",['limit'=>10,'Offset' => $ofset]);//,(L_UpdateDate=".$nowDate."-".$preDate.")
        // return $results->getTotalResultsCount();
        \Log::info("update RA_2 processing....");
        $alldata= $results->toArray();
        foreach($alldata as $item){
            // return $item;
             $this->formate_data($item,$check['id']);
        }
        \Log::info("update RA_2 stoping.....");
        NewUpdateCheker::where('id', $check['id'])->update(['radata_status' => 'stop']);
       
        return 'success';
        // } catch (\Exception $e) {
        //     $do = json_encode($e);
        //     ErrorStore::create(["data" => $do,"type"=>'RA_2_alldata']);

        //     NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'stop']);
        //     return 'fail';
        // }
    }
    
    public function updateRD_1Data(){
    
        $now = new \DateTime();
        $start =  $now->format('Y-m-d\TH:i:s');
        $finale =  date_sub($now, new \DateInterval("PT5M"));
        $end =  $finale->format('Y-m-d\TH:i:s');
        // update offset getting ra_2count rd_1count rd_1count
         $check = NewUpdateCheker::first();
        
         if ($check && $check['rddata_status'] == 'Running') {
            // \Log::info("stop running ra2");
            return 1;
        }
        NewUpdateCheker::where('id', $check['id'])->update(['rddata_status' => 'Running']);
        set_time_limit(2000000);
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
            ->setUsername('RETSARVING')
            ->setPassword('wjq6PJqUA45EGU8')
            ->setRetsVersion('1.7.2');
        \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
        $rets = new \PHRETS\Session($config);
        $connect = $rets->Login();
      
        // try {   
        // $ofset=$check['rd_1count'];
        $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$end."-".$start.")");//
        // $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=2021-04-12T00:00:00-2021-09-12T00:00:00)");//,(L_UpdateDate=".$nowDate."-".$preDate.")
        // $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$nowDate."-".$preDate.")",['limit'=>10,'Offset' => $ofset]);//,(L_UpdateDate=".$nowDate."-".$preDate.")

        $alldata= $results->toArray();
        foreach($alldata as $item){
            $this->formate_data($item,$check['id']);
            // $ofset++;
        }
        NewUpdateCheker::where('id', $check['id'])->update(['rddata_status' => 'stop']);
        return 'success';
        // } catch (\Exception $e) {

        //     NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'stop']);
        //      return 'fail';
        // }
    }


    // method for formating listing data
    public function formate_data($data,$id){
          $ss = json_encode($data);
       \Log::info('Fromating start ....');
        $d = [
            'listingID' => isset($data['L_ListingID'])?$data['L_ListingID']:null,
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
            // 'updated_at' => Carbon::now()
        ];
       \Log::info('updateing database start ....');

        Listing::where('listingID',$data['L_ListingID'])->update($d);
       \Log::info('updateing database end ....');

        try {
            \Log::info('updateing servrver start ....');
            $l = json_decode(json_encode($d), true);

            $client2 = new \GuzzleHttp\Client();
            $request2 = (string) $client2->post('https://m.youhome.cc/updateDataFromDataServer', ['form_params' => $l])->getBody();
            
            NewUpdate::create(['listingId'=>$data['L_ListingID']]);
            // NewUpdateCheker::where('id', $id)->update(['ra_2count'=>$ofset]);
            \Log::info('updateing servrver end ....');
        } catch (\Exception $e) {
            \Log::info('updateing servrver error kaisi ....');
            // $d =['listingId'=>$retsData['listingID']];
            $do = json_encode(['listingId'=>$data['L_ListingID']]);
                
            ErrorStore::create(["data" => $do,"type"=>'RA_2 not updated']);
            // \Log::info($e);
            return $e;
        }
    
    }

    public function updateImageRA_2(){
        $now = new \DateTime();
       $start =  $now->format('Y-m-d\TH:i:s');
       $finale =  date_sub($now, new \DateInterval("PT5M"));
       $end =  $finale->format('Y-m-d\TH:i:s');
        $check = NewUpdateCheker::first();
         if ($check && $check['rd_status'] == 'Running') {
            return 1;
        }
        NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'Running']);
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
        // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_Last_Photo_updt=".$nowDate."-".$preDate.")",['limit'=>1]);
        
        // $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE), (L_Last_Photo_updt=2021-04-06T00:00:00-2021-09-13T00:00:00)",['select'=>'L_ListingID']);
        $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),  (L_Last_Photo_updt=".$end."-".$start.")",['select'=>'L_ListingID']);
        
       
        $alldata  = $results->toArray();
        // return $results->getTotalResultsCount();
        foreach($alldata as $key => $val){
            $isExist = Listing::where('listingID',$val['L_ListingID'])->select('listingID')->first();
           if(!$isExist){

           }
           else{
            $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 0);
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
                
                     ErrorStore::create(["data" => $do,'type'=>'rd_image']);
                }
            $data = json_encode($data);

            $s = DB::table('listings')
            ->where('listingID', $val['L_ListingID'])
            ->update([
                'thumbnail' => $img,
                'images' => $data,
                'updated_at' => Carbon::now(),
                'completed' => 3
            ]);
            $ob = [
                'listingID' =>  $val['L_ListingID'],
                'thumbnail' => $img,
                'images' => $data
            ];
                try{
                    $client2 = new \GuzzleHttp\Client();
                    $request2 = (string) $client2->post('https://m.youhome.cc/storeImageDataFromDataServer', ['form_params' => $ob])->getBody();
                    $json2 = json_decode($request2);
                    NewUpdate::create(['listingId'=>$val['L_ListingID']]);

                } catch (\Exception $e) {
                    \Log::info($e);
                    return false;
                }
            }
        }
        NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'stop']);
        return "success";
    }

    

    public function updateImageRD_1(){
        // return "hell";

       $now = new \DateTime();
       $start =  $now->format('Y-m-d\TH:i:s');
       $finale =  date_sub($now, new \DateInterval("PT5M"));
       $end =  $finale->format('Y-m-d\TH:i:s');
    //    return [$start,$end];

        $check = NewUpdateCheker::first();
         if ($check && $check['ra_status'] == 'Running') {
            return 1;
        }
        NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'Running']);
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
        $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_Last_Photo_updt=".$end."-".$start.")",['select'=>'L_ListingID']);
        $alldata  = $results->toArray();
        foreach($alldata as $key => $val){
            $isExist = Listing::where('listingID',$val['L_ListingID'])->select('listingID')->first();
           if(!$isExist){

           }
           else{
            $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 0);
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
                
                     ErrorStore::create(["data" => $do,'type'=>'rd_image']);
                }
            $data = json_encode($data);

            $s = DB::table('listings')
            ->where('listingID', $val['L_ListingID'])
            ->update([
                'thumbnail' => $img,
                'images' => $data,
                'updated_at' => Carbon::now(),
                'completed' => 3
            ]);
            $ob = [
                'listingID' =>  $val['L_ListingID'],
                'thumbnail' => $img,
                'images' => $data
            ];
                try{
                    $client2 = new \GuzzleHttp\Client();
                    $request2 = (string) $client2->post('https://m.youhome.cc/storeImageDataFromDataServer', ['form_params' => $ob])->getBody();
                    $json2 = json_decode($request2);
                    NewUpdate::create(['listingId'=>$val['L_ListingID']]);

                } catch (\Exception $e) {
                    \Log::info($e);
                    return false;
                }
            }
        }
        NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'stop']);
        return "success";
      

    }

    public function testMethod(Request $request){
        $data = $request->all();
        $str = 'RA_2';
        if($data['str']){
            $str =$data['str'];
        }
        $now = new \DateTime();
        $start =  $now->format('Y-m-d\TH:i:s');
        $finale =  date_sub($now, new \DateInterval("PT720M"));
        $end =  $finale->format('Y-m-d\TH:i:s');
        //    return [$start,$end];
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
        $results=[];
        if($str=='RA_2')
        $results   = $rets->Search('Property',  $str, "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$end."-".$start.")",['select'=>'L_UpdateDate,L_ListingID']);//
        else 
        $results   = $rets->Search('Property',  $str, "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$end."-".$start.")",['select'=>'L_UpdateDate,L_ListingID']);//
        //    $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_ListingID=262639579)");
        // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_UpdateDate=".$end."-".$start.")",['select'=>'L_UpdateDate']);
        $alldata  = $results->toArray();
        // return "je";
    //    return $alldata;
        $dd =[];
       foreach($alldata as $key => $val){
        $isExist = Listing::where('listingID',$val['L_ListingID'])->select('listingID')->first();
       if($isExist){
        array_push($dd, $val);
       }
    }
       return $dd ;
    }

    public function createmissingrequest(){
        $data = Listing::where('listingAddress','')->get();
        foreach($data as $key => $d){
            $ob = [
                'list_id' => $d['id'],
                'listingID' => $d['listingID'],
                'listingAddress' => isset($d['listingAddress'])?$d['listingAddress']:''
            ];
            MapMissingRequest::create($ob);
        }
        return $data;

    }

}
