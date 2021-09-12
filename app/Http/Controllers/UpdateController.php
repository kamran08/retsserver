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

class UpdateController extends Controller
{
    //
    public function updateRa2Data(){
        // $d = Listing::select('updateDate')->where('class', 'RA_2')->orderBy('updateDate','desc')->first();
        // $now = new \DateTime();
        // $date = new DateTime($d['updateDate']);
        // $preDate= $date->format('Y-m-d\TH:i:s');
        // $nowDate =$now->format('Y-m-d\TH:i:s');


    
         $check = NewUpdateCheker::first();
        //  $checklisting = Listing::where('class', 'RA_2')->count();
        //  if($check['ra_2count']>=$checklisting){
        //     NewUpdateCheker::where('id', $check['id'])->update(['ra_2count'=>0]);
        //      return 1;
        //  }
         if ($check && $check['ra_status'] == 'Running') {
            // \Log::info("stop running ra2");
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
      
        try {   
        // $ofset=$check['ra_2count'];
        $results  = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=2021-04-12T00:00:00-2021-09-12T00:00:00)");//,(L_UpdateDate=".$nowDate."-".$preDate.")
        // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$nowDate."-".$preDate.")",['limit'=>10,'Offset' => $ofset]);//,(L_UpdateDate=".$nowDate."-".$preDate.")
        // return $results->getTotalResultsCount();
        // $alldata= $results->toArray();
        foreach($alldata as $item){
            return $item;
            $this->formate_data($item,$check['id']);
        }
        NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'stop']);
        return 'success';
        } catch (\Exception $e) {
            $do = json_encode($e);
            ErrorStore::create(["data" => $do,"type"=>'RA_2_alldata']);

            NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'stop']);
            return 'fail';
        }
    }
    
    public function updateRD_1Data(){
        $d = Listing::select('updateDate')->where('class', 'RD_1')->orderBy('updateDate','desc')->first();
        $now = new \DateTime();
        $date = new DateTime($d['updateDate']);
        $preDate= $date->format('Y-m-d\TH:i:s');
        $nowDate =$now->format('Y-m-d\TH:i:s');
        // update offset getting ra_2count rd_1count rd_1count
         $check = NewUpdateCheker::first();
         $checklisting = Listing::where('class', 'RD_1')->count();
         if($check['rd_1count']>=$checklisting){
            NewUpdateCheker::where('id', $check['id'])->update(['rd_1count'=>0]);
             return 1;
         }
         if ($check && $check['rd_status'] == 'Running') {
            // \Log::info("stop running ra2");
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
      
        try {   
        $ofset=$check['rd_1count'];
        $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$nowDate."-".$preDate.")",['limit'=>10,'Offset' => $ofset]);//,(L_UpdateDate=".$nowDate."-".$preDate.")

        $alldata= $results->toArray();
        foreach($alldata as $item){
            $this->formate_data($item,$ofset,$check['id']);
            $ofset++;
        }
        NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'stop']);
        return 'success';
        } catch (\Exception $e) {
            NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'stop']);
             return 'fail';
        }
    }


    // method for formating listing data
    public function formate_data($retsData,$id){
        $json_data = json_encode($retsData);
      
        $dd=['json_data' => $json_data];
        if($retsData['L_ListingID']) $dd['listingType'] = $retsData['L_ListingID'];
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
        Listing::where('listingID',$serverData['L_ListingID'])->update($dd);
        try {
            $l = json_decode(json_encode($dd), true);

            $client2 = new \GuzzleHttp\Client();
            $request2 = (string) $client2->post('https://m.youhome.cc/updateDataFromDataServer', ['form_params' => $l])->getBody();
            // NewUpdateCheker::where('id', $id)->update(['ra_2count'=>$ofset]);

        } catch (\Exception $e) {
            // $d =['listingId'=>$retsData['listingID']];
            $do = json_encode(['listingId'=>$retsData['L_ListingID']]);
                
            ErrorStore::create(["data" => $do,"type"=>'RA_2_formate']);
            // \Log::info($e);
            return "error";
        }
    
    }

    public function updateImageRA_2(){
        $d = Listing::select('lastPhotoUpdate')->orderBy('lastPhotoUpdate','desc')->first();
        $now = new \DateTime();
        $date = new DateTime($d['lastPhotoUpdate']);
        $preDate= $date->format('Y-m-d\TH:i:s');
        $nowDate =$now->format('Y-m-d\TH:i:s');
       
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
        
        $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE), (L_Last_Photo_updt=2021-04-06T00:00:00-2021-09-12T00:00:00)",['select'=>'L_ListingID']);
        $alldata  = $results->toArray();
        foreach($alldata as $key => $val){
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
                
                     ErrorStore::create(["data" => $do]);
                }
            $data = json_encode($data);

            $s = DB::table('listings')
            ->where('listingID', $val['L_ListingID'])
            ->update([
                'thumbnail' => $img,
                'images' => $data,
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
                } catch (\Exception $e) {
                    \Log::info($e);
                    return false;
                }
        }
        return "success";
      

    }

    

    public function updateImageRD_1(){
        $d = Listing::select('lastPhotoUpdate')->orderBy('lastPhotoUpdate','desc')->first();
        $now = new \DateTime();
        $date = new DateTime($d['lastPhotoUpdate']);
        $preDate= $date->format('Y-m-d\TH:i:s');
        $nowDate =$now->format('Y-m-d\TH:i:s');
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
        $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_Last_Photo_updt=2021-04-06T00:00:00-2021-09-12T00:00:00)",['select'=>'L_ListingID'],['limit'=>1]);
        $alldata  = $results->toArray();
        foreach($alldata as $key => $val){
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
                
                     ErrorStore::create(["data" => $do]);
                }
            $data = json_encode($data);

            $s = DB::table('listings')
            ->where('listingID', $val['L_ListingID'])
            ->update([
                'thumbnail' => $img,
                'images' => $data,
                'completed' => 3
            ]);
            $ob = [
                'listingID' =>  $val['L_ListingID'],
                'thumbnail' => $img,
                'images' => $data
            ];
            return $ob;
                try{
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

}
