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
use App\DisplayUpadateChecker;
use App\MapRequest;
use App\DisplayUpadate;
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
        $now = new \DateTime();
        $start =  $now->format('Y-m-d\TH:i:s');

        $finale =  date_sub($now, new \DateInterval("PT40M"));
        $end =  $finale->format('Y-m-d\TH:i:s');
        
        $check = NewUpdateCheker::first();
         
         if ($check && $check['radata_status'] == 'Running') {
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
        $results =[];
       $updateCheck = DisplayUpadateChecker::create(['class'=>'RA_2','startName'=>'ra_start_'.$now->format('Y-m-d'),'startTime'=>new \DateTime(),'endName'=>'ra_stop_'.$now->format('Y-m-d')]);

        $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$end."-".$start.")");//

        $alldata= $results->toArray();
        // return  $alldata;
        $total= $results->getTotalResultsCount();
        \Log::info('ra start');
        \Log::info($total);
        return $total;
        DisplayUpadate::create(['displayId'=>$total,'L_Address'=>'ra_start_'.$now->format('Y-m-d')]);

        foreach($alldata as $item){
            $isExist = Listing::where('displayId',$item['L_DisplayId'])->select('displayId')->first();
            $this->formate_data($item,$check['id'],$isExist);
        }
        DisplayUpadateChecker::where('id', $updateCheck['id'])->update(['endTime'=>new \DateTime()]);

        DisplayUpadate::create(['displayId'=>$total,'L_Address'=>'ra_stop_'.$now->format('Y-m-d')]);
        NewUpdateCheker::where('id', $check['id'])->update(['radata_status' => 'stop']);
        \Log::info('ra end');
        
        return 'success';
    }
    
    public function updateRD_1Data(){

        $now = new \DateTime();
        $start =  $now->format('Y-m-d\TH:i:s');

        $finale =  date_sub($now, new \DateInterval("PT40M"));
        $end =  $finale->format('Y-m-d\TH:i:s');
        
        $check = NewUpdateCheker::first();
         
         if ($check && $check['rddata_status'] == 'Running') {
            return 1;
        }
        \Log::info('rd start');
        
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
        $results =[];
       $updateCheck = DisplayUpadateChecker::create(['class'=>'RD_1','startName'=>'rd_start_'.$now->format('Y-m-d'),'startTime'=>new \DateTime(),'endName'=>'rd_stop_'.$now->format('Y-m-d')]);

        // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$end."-".$start.")");//
        $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$end."-".$start.")");//

        $alldata= $results->toArray();
        // return  $alldata;
        
        $total= $results->getTotalResultsCount();
        \Log::info('rd start');
        \Log::info($total);
        return $total;
        DisplayUpadate::create(['displayId'=>$total,'L_Address'=>'rd_start_'.$now->format('Y-m-d')]);

        foreach($alldata as $item){
            $isExist = Listing::where('displayId',$item['L_DisplayId'])->select('displayId')->first();
            $this->formate_data($item,$check['id'],$isExist);
        }
        DisplayUpadateChecker::where('id', $updateCheck['id'])->update(['endTime'=>new \DateTime()]);
        \Log::info('rd end');

        DisplayUpadate::create(['displayId'=>$total,'L_Address'=>'rd_stop_'.$now->format('Y-m-d')]);
        NewUpdateCheker::where('id', $check['id'])->update(['rddata_status' => 'stop']);
        return 'success';
        
    }


    // method for formating listing data
    public function formate_data($data,$id,$exist){
          $ss = json_encode($data);
       \Log::info('Fromating start ....');
             $d['completed']=1;
             $d['json_data']=  $ss;
             $d['listingID']=isset($data['L_ListingID'])?$data['L_ListingID']:null;
            
             if ($data['LM_Char10_11'] == 'House/Single Family') {
                $data['LM_Char10_11'] = 'House';
            } else if ($data['LM_Char10_11'] == 'Apartment/Condo') {
                $data['LM_Char10_11'] = 'Condo';
            } else if ($data['LM_Char10_11'] == 'Townhouse') {
                $data['LM_Char10_11'] = 'Townhouse';
            } else if ($data['LM_Char10_11'] == '1/2 Duplex') {
                $data['LM_Char10_11'] = 'Duplex';
            }

            if(isset($data['L_Type_'])) $d['listingType']=$data['L_Type_'];
             if(isset($data['L_Area'])) $d['listingArea']=$data['L_Area'];
             if(isset($data['L_Address'])) $d['listingAddress']=$data['L_Address'];
             if(isset($data['L_AddressDirection'])) $d['listingAddressDirection']=$data['L_AddressDirection'];
             if(isset($data['L_AddressStreet'])) $d['listingAddressStreet']=$data['L_AddressStreet'];
             if(isset($data['L_AddressUnit'])) $d['listingAddressUnit']=$data['L_AddressUnit'];
             if(isset($data['LFD_Amenities_25'])) $d['amenities']=$data['LFD_Amenities_25'];
             if(isset($data['LFD_BasementArea_6'])) $d['basementArea']=$data['LFD_BasementArea_6'];
             if(isset($data['LM_char30_28'])) $d['lotSizeLenth']=$data['LM_char30_28'];
             if(isset($data['LV_vow_address'])) $d['onInternet']=$data['LV_vow_address'];
             if(isset($data['LFD_FeaturesIncluded_24'])) $d['features']=$data['LFD_FeaturesIncluded_24'];
             if(isset($data['LM_Int1_2'])) $d['fireplaces']=$data['LM_Int1_2'];
             if(isset($data['LM_Dec_7'])) $d['floorAreaTotal']=$data['LM_Dec_7'];
             if(isset($data['LM_Dec_8'])) $d['lotSizeWidthFeet']=$data['LM_Dec_8'];
             if(isset($data['LM_Dec_9'])) $d['lotSizeMeter']=$data['LM_Dec_9'];
             if(isset($data['LR_remarks33'])) $d['internetRemarks']=$data['LR_remarks33'];
             if(isset($data['L_ListingDate'])) $d['listingDate']=$data['L_ListingDate'];
             if(isset($data['L_UpdateDate'])) $d['updateDate']=$data['L_UpdateDate'];
             if(isset($data['L_AddressNumber'])) $d['addressNumber']=$data['L_AddressNumber'];
             if(isset($data['L_City'])) $d['city']=$data['L_City'];
             if(isset($data['LM_Char10_5'])) $d['subArea']=$data['LM_Char10_5'];
             if(isset($data['L_State'])) $d['state']=$data['L_State'];
             if(isset($data['L_Zip'])) $d['zip']=$data['L_Zip'];
             if(isset($data['L_AskingPrice'])) $d['askingPrice']=$data['L_AskingPrice'];
             if(isset($data['LM_Dec_16'])) $d['grossTaxes']=$data['LM_Dec_16'];
             if(isset($data['LM_Dec_12'])) $d['lotSizeArea']=$data['LM_Dec_12'];
             if(isset($data['LM_Dec_13'])) $d['lotSizeAreaSqMt']=$data['LM_Dec_13'];
             if(isset($data['LM_Dec_11'])) $d['lotSizeAreaSqFt']=$data['LM_Dec_11'];
             if(isset($data['L_DisplayId'])) $d['displayId']=$data['L_DisplayId'];
             if(isset($data['LM_Int1_1'])) $d['floorLevel']=$data['LM_Int1_1'];
             if(isset($data['L_PictureCount'])) $d['pictureCount']=$data['L_PictureCount'];
             if(isset($data['L_Last_Photo_updt'])) $d['lastPhotoUpdate']=$data['L_Last_Photo_updt'];
             if(isset($data['L_Status'])) $d['status']=$data['L_Status'];
             if(isset($data['LM_Char10_11'])) $d['houseType']=$data['LM_Char10_11'];
             if(isset($data['LM_Int1_4'])) $d['totalBedrooms']=$data['LM_Int1_4'];
             if(isset($data['LM_Int1_7'])) $d['totalRooms']=$data['LM_Int1_7'];
             if(isset($data['LM_Int1_17'])) $d['halfBaths']=$data['LM_Int1_17'];
             if(isset($data['LM_Int1_18'])) $d['fullBaths']=$data['LM_Int1_18'];
             if(isset($data['LM_Int1_19'])) $d['totalBaths']=$data['LM_Int1_19'];
             if(isset($data['LM_Int2_3'])) $d['age']=$data['LM_Int2_3'];
             if(isset($data['LM_Int2_2'])) $d['yearBuilt']=$data['LM_Int2_2'];
             if(isset($data['LM_Int2_5'])) $d['texPerYear']=$data['LM_Int2_5'];
             if(isset($data['LM_Int4_1'])) $d['unitsInDevelopment']=$data['LM_Int4_1'];
             if(isset($data['LM_Int1_8'])) $d['kitchens']=$data['LM_Int1_8'];
             if(isset($data['L_PricePerSQFT'])) $d['perSqrtPrice']=$data['L_PricePerSQFT'];
             if(isset($data['LO1_OrganizationName'])) $d['organizationName1']=$data['LO1_OrganizationName'];
             if(isset($data['LO2_OrganizationName'])) $d['organizationName2']=$data['LO2_OrganizationName'];
             if(isset($data['LM_Dec_22'])) $d['startaFee']=$data['LM_Dec_22'];
             if(isset($data['L_OriginalPrice'])) $d['originalListPrice']=$data['L_OriginalPrice'];
             if(isset($data['L_SoldPrice'])) $d['soldPrice']=$data['L_SoldPrice'];
             if(isset($data['LM_int4_40'])) $d['previousPrice']=$data['LM_int4_40'];
             if(isset($data['LM_Dec_24'])) $d['soldPricePerSqrt']=$data['LM_Dec_24'];
            $d ['updated_at'] = Carbon::now();
            $d['class'] ='RA_2';
             if(!$exist){
            try {
            if($data['L_Status']=='Terminated'){
                  return  DisplayUpadate::create(['displayId'=>$data['L_DisplayId'],'L_Address'=>'Terminated not exit']);
            }

            
            Listing::create($d);
            DisplayUpadate::create(['displayId'=>$data['L_DisplayId'],'L_Address'=>'new data']);
            return 1;
                } catch (\Exception $e) {
                    return  DisplayUpadate::create(['displayId'=>$data['L_DisplayId'],'L_Address'=>'not exit error']);

                }

        }
       \Log::info('updateing database start ....');
       try {
            if($data['L_Status']=='Terminated'){
                Listing::where('displayId',$data['L_DisplayId'])->delete();
                DisplayUpadate::create(['displayId'=>$data['L_DisplayId'],'L_Address'=>'deleted']);
                // return 2;
            }
            else{
                Listing::where('displayId',$data['L_DisplayId'])->update($d);
                DisplayUpadate::create(['displayId'=>$data['L_DisplayId'],'L_Address'=>$data['L_Address']]);
                // return 3;
            }

        } catch (\Exception $e) {
            $a = isset(data['displayId'])?data['displayId']:'untrace';
            DisplayUpadate::create(['displayId'=>$a,'L_Address'=>'error']);
            // return 4;

        }

       \Log::info('updateing database end ....');
    //    NewUpdate::create(['listingId'=>$data['L_ListingID'],'L_Address'=>$data['L_Address']]);


        // try {
        //     \Log::info('updateing servrver start ....');
        //     $l = json_decode(json_encode($d), true);

        //     $client2 = new \GuzzleHttp\Client();
        //     $request2 = (string) $client2->post('https://m.youhome.cc/updateDataFromDataServer', ['form_params' => $l])->getBody();
            
        //     NewUpdate::create(['listingId'=>$data['L_ListingID']]);
        //     // NewUpdateCheker::where('id', $id)->update(['ra_2count'=>$ofset]);
        //     \Log::info('updateing servrver end ....');
        // } catch (\Exception $e) {
        //     \Log::info('updateing servrver error kaisi ....');
        //     // $d =['listingId'=>$retsData['listingID']];
        //     $do = json_encode(['listingId'=>$data['L_ListingID']]);
                
        //     ErrorStore::create(["data" => $do,"type"=>'RA_2 not updated']);
        //     // \Log::info($e);
        //     return $e;
        // }
    
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

            $this->removeAllPreviousImages($val['L_ListingID']);
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
        $now = new \DateTime($data['start']);
        $start =  $now->format('Y-m-d\TH:i:s');
        // $finale =  date_sub($now, new \DateInterval("PT720M"));
        // $end =  $finale->format('Y-m-d\TH:i:s');
        
        
        $a = new \DateTime($data['end']);
        $end = $a->format('Y-m-d\TH:i:s');
        //    return [$start,$end];
        // return $data;

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
        $results   = $rets->Search('Property',  $str, "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$end."-".$start.")",['select'=>'L_UpdateDate,L_ListingID'],['limit'=>1]);//
        else 
        $results   = $rets->Search('Property',  $str, "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$end."-".$start.")",['select'=>'L_UpdateDate,L_ListingID'],['limit'=>1]);//
        //    $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_ListingID=262639579)");
        // $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_UpdateDate=".$end."-".$start.")",['select'=>'L_UpdateDate']);
        // $results   = $rets->Search('Property', 'RA_2', "(L_ListingID=262580429)");//
        
        
        $alldata  = $results->toArray();
        // return "je";
        return $results->getTotalResultsCount();
       return $alldata;
        $dd =[];
       foreach($alldata as $key => $val){
        // $isExist = Listing::where('listingID',$val['L_ListingID'])->select('listingID')->first();
        //     if($isExist){
        //     }
         array_push($dd, $val);
    }
       return $dd ;
    }

    public function rdupdatefrom2021(Request $request){
     

        $data = $request->all();
 
    
        $now = new \DateTime();
        $start =  $now->format('Y-m-d\TH:i:s');

        $finale =  date_sub($now, new \DateInterval("PT30M"));
        $end =  $finale->format('Y-m-d\TH:i:s');
        // return [$start,$end];
        
        // $now = new \DateTime('2021-10-06T24:00:00');
        // $start =  $now->format('Y-m-d\TH:i:s');
        // $a = new \DateTime('2021-10-06T00:00:00');
        // $end = $a->format('Y-m-d\TH:i:s');

        // update offset getting ra_2count rd_1count rd_1count
         $check = NewUpdateCheker::first();
         
        
       
        // NewUpdateCheker::where('id', $check['id'])->update(['rddata_status' => 'Running']);
        set_time_limit(2000000);
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
            ->setUsername('RETSARVING')
            ->setPassword('wjq6PJqUA45EGU8')
            ->setRetsVersion('1.7.2');
        \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
        $rets = new \PHRETS\Session($config);
        $connect = $rets->Login();
        $results =[];
        // NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'Running']);

        $results   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|APTU,DUPXH,TWNHS),(L_UpdateDate=".$end."-".$start.")");//
        // $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|HOUSE),(L_UpdateDate=".$end."-".$start.")");//

        $alldata= $results->toArray();
        // return  $alldata;
        $total= $results->getTotalResultsCount();
        return $total;
        DisplayUpadate::create(['displayId'=>$total,'L_Address'=>'RA_2 2nd start']);

        foreach($alldata as $item){
            $isExist = Listing::where('displayId',$item['L_DisplayId'])->select('displayId')->first();
            $this->formate_data($item,$check['id'],$isExist);
        }
        // NewUpdateCheker::where('id', $check['id'])->update(['ra_status' => 'stop']);
        DisplayUpadate::create(['displayId'=>$total,'L_Address'=>'RA_2 2nd off']);

        return 'success';

    }


    public function storeImages(Request $request){
        $reqD = $request->all();
  

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
        // $check = NewUpdateCheker::first();
        // NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'Running']);
        \Log::info('start');
    
        // $q = ;
        // if(isset($reqD['id'])){
        //         $q->where('id','<',$reqD['id']);
        //     }
       $alldata = Listing::select('id', 'listingID')->doesnthave('missed_up')->orderBy('id','desc')->get();

    //    return sizeof($alldata);
        foreach($alldata as $key => $val){
            $check = NewUpdate::where('listingId',$val['listingID'])->first();
            if($check) {
                \Log::info("faisi");
                continue;
            }
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
                    // return $img;
                    $l=2;
                    $myFile = Storage::disk('spaces')->put($name, $url);
                    Storage::disk('spaces')->setVisibility($name, 'public');
                    $ll = Storage::disk('spaces')->url($name);
                    array_push($data, $ll);
                }
            } catch (\Exception $e) {
                    $do = json_encode($val);
                    ErrorStore::create(["data" => $do,'type'=>$val['listingID']]);
            }
            $data = json_encode($data);
            NewUpdate::create(['listingId'=>$val['listingID'],'L_Address'=>'imageupdate']);
            
            $s = DB::table('listings')
            ->where('id', $val['id'])
            ->update([
                'thumbnail' => $img,
                'images' => $data,
            ]);
        }
        \Log::info('end');

        // NewUpdateCheker::where('id', $check['id'])->update(['rd_status' => 'stop']);

    }
    



    public function removeAllPreviousImages(){
        // $data = Listing::where('listingID',$id)->select('listingID','thumbnail','images')->first();
        // $images = json_decode($data['images']);
        // $a = $data['thumbnail'];
        // array_push($images, $a);
        // foreach($images as $key => $url){
               $url ='https://youhomespace.nyc3.digitaloceanspaces.com/allfiles/cover.jpg';
               $parts = explode('/', $url);
                $path = end($parts);
                return $path;
                // if(Storage::disk('spaces')->exists($path)) {

                //     $d= Storage::disk('spaces')->delete($path);
                //     return 'from space';
                //     // Storage::disk('spaces')->files('');
                // }
                if(Storage::disk('spaces3')->exists($path)) {
                    $d= Storage::disk('spaces3')->delete($path);
                    return 'from space3';

                    // Storage::disk('spaces')->files('');
                }
                
                if(Storage::disk('spaces2')->exists($path)) {
                    $d= Storage::disk('spaces2')->delete($path);
                    return 'from space2';

                }
        // }
    //    return Listing::where('listingID',$id)->update(['thumbnail'=>null, 'images'=>null]);
    }
    public function testdelete(){
        return "hello";
    }



}

