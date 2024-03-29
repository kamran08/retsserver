<?php

namespace App\Http\Controllers;
date_default_timezone_set('America/New_York');

use Illuminate\Http\Request;
use App\Picture;
use App\JsonData;
use App\Listing;
use App\MapRequest;
use App\Checker;
use App\MapMissingRequest;
use App\UpdateChecker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use File;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DataController extends Controller
{


       

    public function deletedublicateData(Request $request){
          try {
        $data = DB::select(DB::raw("SELECT listingID FROM listings GROUP BY listingID HAVING COUNT(listingID) > 1"));

            foreach( $data as $key => $item1) {
                $item= json_encode($item1);
                
                     $istrue = false;
                     $id= '';
                $lists =  Listing::select('id','listingID', 'images')->where('listingID', $item1->listingID)->get();
                foreach($lists as $list) {
                        if($list->images){
                            $id = $list->id;
                            $istrue = true;
                            break;
                        }
                    }
                    if(!$istrue) $id = $lists[0]['id'];
                    Listing::whereNotIn('id',[$id])->where('listingID', $item1->listingID)->delete();

                }
                 return "success";
                } catch (\Exception $e) {
                    \Log::info($e);
                    return false;
                }
            }
    
    public function getLocationTest()
    {
        // $alldata = Listing::where('lat', null)->orWhere('lang', null)->select('id', 'lat', 'lang', 'listingAddress')->limit(100)->get();
        // $date =   date("Y-m-d");
        // $mapreq = MapRequest::where('date', $date)->first();
        // if ($mapreq) {
        // } else {
        //     $mapreq = MapRequest::create([
        //         "counter" => 0,
        //         "date" => $date
        //     ]);
        // }
        $d =[];
        $d['listingAddress'] = '898 E 11TH STREET';
            // if ($mapreq['counter'] >= 2000) return 1;

            if ($d['listingAddress']) {
                $d['listingAddress'] = trim($d['listingAddress'], "#");

                $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $d['listingAddress'] . ',canada')->getBody();
                $json = json_decode($request);
                $lat = null;
                $lang = null;
                if (sizeof($json->results) > 0) {
                    $lat = $json->results[0]->geometry->location->lat;
                    $lang = $json->results[0]->geometry->location->lng;
                } 
                else {
                    $ob=[
                     'list_id'=>'1',
                    'listingID'=>'123',
                    'listingAddress'=> $d['listingAddress']
                    ];
                MapMissingRequest::create($ob);
                }
  
            }
            
        return response()->json(['lat'=>$lat,'lang'=>$lang]);
        
        // return "success";
    } 
    public function getOpenHouseData(Request $request){
       /* $id=$request->id;
        $s = Listing::where('id', $id)->where('lat', '!=', null)->first();
        \Log::info($s);
        if ($s) {
            try {
                $l = json_decode(json_encode($s), true);
                // $request2 = Http::post('https://youhome.cc/storeDataFromDataServer', $s);
                // return 1;

                $client2 = new \GuzzleHttp\Client();
                $request2 = (string) $client2->post('https://m.youhome.cc/storeDataFromDataServer', ['form_params' => $l])->getBody();
                // $json2 = json_decode($request2);

            } catch (\Exception $e) {
                \Log::info($e);
                return "error";
            }
        }
            return "pl";
        $d = $date =   date("Y-m-d");
        $dd = MapRequest::where('date', $d)->first();
        if($dd) {
            return $dd['counter'];
        }
        MapRequest::create([
            "counter" =>0,
            "date"=> $d
        ]);
        return $d;*/
        // $data = Listing::select('id', 'listingID')->where('class','RD_1')->limit(1)->get();
        set_time_limit(2000000);
        $config = new \PHRETS\Configuration;
        $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
        ->setUsername('RETSARVING')
        ->setPassword('wjq6PJqUA45EGU8')
        ->setRetsVersion('1.7.2');
        \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
        $rets = new \PHRETS\Session($config);
        $connect = $rets->Login();
        $data1 = [];
        $result   = $rets->Search('Property',  'RA_2', "(L_ListingID=262455387)", ['Limit'  =>  1]);
        // $result = $rets->Search("Property", "RD_1", '*', ['Limit'    =>    1]);
        // foreach ($data as $key => $val) {
        //     //    $url=  $rets->GetObject('OpenHouse', 'RD_1', '*',1);
        //     //   $url=  $rets->Search("OpenHouse", "RD_1",'*', ['Limit'    =>    1]);

        //     // $url =  $rets->Search('OpenHouse', 'RD_1','*',['Limit'    =>    20]);
        //     // $url = $rets->GetObject('OpenHouse', 'RD_1', , '*',1);
        //     $url = $rets->Search('OpenHouse', 'RD_1', ['Limit'    =>    20]);
        //     array_push($data1, $url);

        // }
        // $url = $rets->Search('OpenHouse', 'RD_1','*', ['Limit'=>20]);
        $alldata  = $result->toArray();
        return $alldata;
        dd($alldata);
        return $data1;

    }
    public function storeDataFromDataServer(Request $request){
        // return "ok";
        try {
            $data = $request->all();
            if (isset($data['id']))  unset($data['id']);
            if (isset($data['thumbnail']))  unset($data['thumbnail']);
            if (isset($data['images']))  unset($data['images']);
            unset($data['created_at']);
            unset($data['updated_at']);
           \Log::info($data['listingID']);

            $ch = Listing::where('listingID', $data['listingID'])->first();
            if ($ch) {
                return Listing::where('listingID', $data['listingID'])->update($data);
            } else {
                return  Listing::create($data);
            }
        } catch (\Exception $e) {
            \Log::info($e);
            return false;
        }
    }
    public function storeImageDataFromDataServer(Request $request){
        // return "ok";
        try {
            $data = $request->all();
            \Log::info($data['listingID']);
            $ch = Listing::where('listingID', $data['listingID'])->first();
            if ($ch) {
                return Listing::where('listingID', $data['listingID'])->update($data);
            } else {
                return  Listing::create($data);
            }
        } catch (\Exception $e) {
            \Log::info($e);
            return false;
        }
    }
    public function uploadThumb(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png,fig,gif,svg'
        ]);
       $classifiedImg = $request->file('file');
       $image = Image::make($classifiedImg)->encode('webp', 90)->resize(200, 250);

        $path1 = $request->file->getClientOriginalName();
        $myFile = Storage::disk('spaces')->put($path1, $image);
        $image->save($path1);
        Storage::disk('spaces')->setVisibility($path1, 'public');
        $files = Storage::disk('spaces')->url($path1);
        return $files;

    }

    public function directoryCheck(){
      $counter = 0;
        $result1=[];
        $result2=[];
        $d = public_path('/uploads');
        $dirs = File::directories(public_path());
        $files = scandir($d);
        foreach ($files as $f) {
            $s = $d . '/' . $f;
            if (ends_with($f, ['.png', '.jpg', '.jpeg', '.gif'])) {
                $counter ++;
                $image = Image::make($s)->encode('webp', 90)->resize(200, 250);
                return $image;
                $myFile = Storage::disk('spaces')->put($f, $image);
                Storage::disk('spaces')->setVisibility($f, 'public');
                $ll = Storage::disk('spaces')->url($f);
                $image->save($f);

                $result1[] = $ll;
              
            }
        //    else if (ends_with($f, ['.svg', '.fig'])) {
        //         $counter++;
        //         $myFile = Storage::disk('spaces')->put($f, file_get_contents($s));
        //         Storage::disk('spaces')->setVisibility($f, 'public');
        //         $ll = Storage::disk('spaces')->url($f);

        //         $result2[] = $ll;
              
        //     }
        }
        return  [
           'one'=> $result1,
            'two'=> $result2,
            'count'=>$counter
        ];
    }

    public function uploadThumb1(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png'
        ]);
        // $picName = time() . '.' . $request->file->getClientOriginalExtension();
        $picName = "one.png";
        $image_resize = Image::make('/uploads/' . $request->file('file'))->encode('webp', 50);
        $filename = rand(111, 99999) . '.' . 'webp';
        $product = $image_resize;
        $product->save($filename);
        return $filename;
    }
  
    public function test(Request $request){
        // return $request->file('file');
        $webp = Webp::make($request->file('file'));
        // return $webp;

        if ($webp->save(public_path('output.jpg'))) {
            return $webp;
        }
    }

    public function getData1(Request $request){






        // $path1 = 'G:\social media by sadek\example-app\public\uploads\asdfgh.png';
 
        // $myFile=Storage::disk('spaces')->put('two3.jpg', file_get_contents($path1));
        
        // Storage::disk('spaces')->setVisibility('two3.jpg', 'public');
        // $files = Storage::disk('spaces')->url('two3.jpg');
        // return $files;
        
        $result = [];
        $dirs = File::directories(public_path());
        return $dirs;
        foreach ($dirs as $dir) {
            $files = File::files($dir);
            $files = File::files("G:\\social media by sadek\\example-app\\public\\uploads");
            foreach ($files as $f) {
            // var_dump($f);
                if (ends_with($f, ['.png', '.jpg', '.jpeg', '.gif','fig'])) {
                    $path= $f->store( 'spaces');
                    return $path;
                    // Storage::disk('spaces')->setVisibility($path, 'public');
                    // $result[] =  Storage::disk('spaces')->url($path);
                    // $path = $f->getPathname();
                    // return $path;
                    // Storage::disk('spaces')->setVisibility($path, 'public');
                    // $image = Image::create([
                    //     'filename' => basename($path),
                    //     'url' => Storage::disk('spaces')->url($path)
                    // ]);
                }
            }
        }
        return $result; //will be in this case ['img/text1_logo.png']
    
    }
    public function storeToBucket(Request $request){




            $path1 = $request->file->getClientOriginalName();
            $myFile = Storage::disk('spaces')->put($path1, file_get_contents($request->file('file')));
            Storage::disk('spaces')->setVisibility($path1, 'public');
            $files = Storage::disk('spaces')->url($path1);
            return $files;


            // $picName = time() . '.' . $request->file->getClientOriginalExtension();


            $picName = $request->file->getClientOriginalName();
            // $path = $request->file('file')->store('','spaces');
            // $path = $request->filemove(public_path('spaces'),$picName);
            $myFile = Storage::disk('spaces')->put('kk.png', $request->file('file'));
            
            // return $path;
        





            // $path = $request->file('file')->store('uploads', 'spaces');
            Storage::disk('spaces')->setVisibility($myFile, 'public');
            // $image = Picture::create([
            //     'filename' => basename($path),
            //     'url' => Storage::disk('spaces')->url($path)
            // ]);
            // return $image;
            return  Storage::disk('spaces')->url($myFile);
            // return Storage::disk('spaces')->url($path);
    }

 

    //
    public function createNewListing($data,$type){
        $ss = json_encode($data);
        if($data['LM_Char10_11']==''){
            
        }
        else if($data['LM_Char10_11']==''){

        }
        else if($data['LM_Char10_11']==''){

        }
        else if($data['LM_Char10_11']==''){

        }

        $d = [
            'listingID'=>$data['L_ListingID'],
            'class'=> $type,
            'listingType'=>$data['L_Type_'],
            // 'listingArea'=>$data['L_Area'],
            'listingArea'=>$data['L_Area'],
            'listingAddress'=>$data['L_Address'],
            'listingAddressDirection'=>$data['L_AddressDirection'],
            'listingAddressStreet'=>$data['L_AddressStreet'],
            'listingAddressUnit'=>$data['L_AddressUnit'],
            'amenities'=>$data['LFD_Amenities_25'],
            'basementArea'=>$data['LFD_BasementArea_6'],
            'lotSizeLenth'=>$data['LM_char30_28'],
            'onInternet'=>$data['LV_vow_address'],
            'features'=>$data['LFD_FeaturesIncluded_24'],
            'fireplaces'=>$data['LM_Int1_2'],
            'floorAreaTotal'=>$data['LM_Dec_7'],
            'lotSizeWidthFeet'=>$data['LM_Dec_8'],
            'lotSizeMeter'=>$data['LM_Dec_9'],
            'internetRemarks'=>$data['LR_remarks33'],
            'listingDate'=>$data['L_ListingDate'],
            'updateDate'=>$data['L_UpdateDate'],
            'addressNumber'=>$data['L_AddressNumber'],
            'city'=>$data['L_City'],
            'subArea'=>$data['LM_Char10_5'],
            'state'=>$data['L_State'],
            'zip'=>$data['L_Zip'],
            'askingPrice'=>$data['L_AskingPrice'],
            'grossTaxes'=>$data['LM_Dec_16'],
            'lotSizeArea'=>$data['LM_Dec_12'],
            'lotSizeAreaSqMt'=>$data['LM_Dec_13'],
            'lotSizeAreaSqFt'=>$data['LM_Dec_11'],
            'displayId'=>$data['L_DisplayId'],
            'floorLevel'=>$data['LM_Int1_1'],
            'pictureCount'=>$data['L_PictureCount'],
            'lastPhotoUpdate'=>$data['L_Last_Photo_updt'],
            'status'=>$data['L_Status'],
            'houseType'=>$data['LM_Char10_11'],
            'lat'=>$data['lat'],
            'lang'=>$data['lang'],
            'totalBedrooms'=>$data['LM_Int1_4'],
            'totalRooms'=>$data['LM_Int1_7'],
            'halfBaths'=>$data['LM_Int1_17'],
            'fullBaths'=>$data['LM_Int1_18'],
            'totalBaths'=>$data['LM_Int1_19'],
            'age'=>$data['LM_Int2_3'],
            'yearBuilt'=>$data['LM_Int2_2'],
            'texPerYear'=>$data['LM_Int2_5'],
            'unitsInDevelopment'=>$data['LM_Int4_1'],
            'kitchens'=>$data['LM_Int1_8'],
            'json_data'=>$ss
        ];
        return Listing::create($d);
    }
    // start  of method getData
    public function getData(){
        // return 1;
        $idd = Checker::first();
        if($idd && $idd['status1']== 'Running') return 1;
        try {
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
            
            $ofset = 0;
            if($idd){
                $ofset = $idd['lastId'];
            }
            // $results   = $rets->Search('Property',  'RA_2', "(L_Area=|29,),(L_Status=1_0),(LM_Char10_11=|HOUSE)", ['Limit'  =>   2, 'Offset'=>$ofset,'select'=> 'L_ListingID,L_Type_,LM_Char10_11,L_AddressNumber,L_AddressDirection,L_AddressStreet,L_AddressNumberLow,L_StreetDesignationId,LM_Int1_1,LM_Int2_2']);
            $results   = $rets->Search('Property',  'RA_2', "(L_Area=|29,),(L_Status=1_0),(LM_Char10_11=|DUPXH)", ['Limit'  =>   2, 'Offset'=>$ofset,'select'=> 'L_ListingID,L_Type_,LM_Char10_11,L_AddressNumber,L_AddressDirection,L_AddressStreet,L_AddressNumberLow,L_StreetDesignationId,LM_Int1_1,LM_Int2_2','L_Address']);
            // $results   = $rets->Search('Property',  'RD_1', "(L_Area=|1,2,3,4,5,7,8,9,10,12,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30),(L_Status=1_0,2_0,4_0,5_1,5_2)", ['Limit'  =>   100, 'Offset' => 73300, 'select' => 'L_ListingID']);
            //  return $results->getTotalResultsCount();
            $alldata  = $results->toArray();
            // \Log::info("finished");
            return $alldata;
            
            foreach ($alldata as $key => $val) {
                // $ss = json_encode($val);
                $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $val['L_Address'])->getBody();;
                $json = json_decode($request);
                $lat = $json->results[0]->geometry->location->lat;
                $lang = $json->results[0]->geometry->location->lng;
                $val['lat'] = $lat;
                $val['lang'] = $lang;
                $jsonV = '';
            
                if (!$jsonV || ( isset($jsonV['L_ListingID']) && $jsonV['L_ListingID'] != $val['L_ListingID'])) {
                    
                    $jsonV  = $this->createNewListing($val, 'RD_1');
                    // $jsonV = JsonData::create(['data' => $ss, 'L_ListingID' => $val['L_ListingID']]);
               
                $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 0);
                $data = [];
                foreach ($objects as $ke => $photo) {
                    $url = $photo->getContent();
                    $name = time() . uniqid(rand()) . '.png';
                    $myFile = Storage::disk('spaces')->put($name, $url);
                    Storage::disk('spaces')->setVisibility($name, 'public');
                    $ll = Storage::disk('spaces')->url($name);
                    array_push($data, $url);
                    Picture::create(['filename' => $ll, 'L_ListingID' => $val['L_ListingID']]);
                    // return 1;
                }

                $ofset++;
            
                Checker::where('id', $idd['id'])->update(['lastId' => $ofset,'status1'=>'Running']);
                }
            }
            // return $ofset;
            Checker::where('id', $idd['id'])->update(['lastId' => $ofset, 'status1' => 'Stop']);
            return "successfully insert data";
        } catch (\Exception $e) {
            Checker::where('id', $idd['id'])->update(['lastId' => $ofset, 'status1' => 'Fail']);
            // return "fail";
            return $e;
        }
    }
    // end   of method getData
    // start  of method getDataTwo
    public function getDataTwo(){
        $idd = Checker::first();
        if($idd && $idd['status2']== 'Running') return 1;
        try {
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
            
            $ofset = 0;
            if($idd){
                $ofset = $idd['lastId'];
            }
            // $results   = $rets->Search('Property',  'RD_1', "(L_Area=|29,),(L_Status=1_0)", ['Limit'  =>   2, 'Offset'=>$ofset,'select'=> 'L_ListingID,L_Type_,LV_vow_address,L_AddressNumber,L_AddressDirection,L_AddressStreet,L_AddressNumberLow,L_StreetDesignationId,LM_Int1_1,LM_Int2_2']);
            $results   = $rets->Search('Property',  'RA_2', "(L_Area=|1,2,3,4,5,7,8,9,10,12,13,14,15,17,18,19,20,21,22,23,24,25,26,27,28,29,30),(L_Status=1_0,2_0,4_0,5_1,5_2)", ['Offset' => $ofset]);

            $alldata  = $results->toArray();
            // \Log::info("finished");
            // return $alldata;
            
            foreach ($alldata as $key => $val) {
                // $ss = json_encode($val);
                $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $val['L_Address'])->getBody();;
                $json = json_decode($request);
                $lat = $json->results[0]->geometry->location->lat;
                $lang = $json->results[0]->geometry->location->lng;
                $val['lat'] = $lat;
                $val['lang'] = $lang;
                $jsonV = '';
            
                if (!$jsonV || ( isset($jsonV['L_ListingID']) && $jsonV['L_ListingID'] != $val['L_ListingID'])) {
                    
                    $jsonV  = $this->createNewListing($val, 'RA_2');
                    // $jsonV = JsonData::create(['data' => $ss, 'L_ListingID' => $val['L_ListingID']]);
               
                $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 0);
                $data = [];
                foreach ($objects as $ke => $photo) {
                    $url = $photo->getContent();
                    $name = time() . uniqid(rand()) . '.png';
                    $myFile = Storage::disk('spaces')->put($name, $url);
                    Storage::disk('spaces')->setVisibility($name, 'public');
                    $ll = Storage::disk('spaces')->url($name);
                    array_push($data, $url);
                    Picture::create(['filename' => $ll, 'L_ListingID' => $val['L_ListingID']]);
                    // return 1;
                }

                $ofset++;
            
                Checker::where('id', $idd['id'])->update(['lastId2' => $ofset,'status2'=>'Running']);
                }
            }
            // return $ofset;
            return "successfully insert data";
        } catch (\Exception $e) {
            Checker::where('id', $idd['id'])->update(['lastId2' => $ofset, 'status2' => 'Fail']);
            return "fail";
        }
    }
    // end of method getDataTwo
   
    public function resorce (){
        
      
        // return $u['address_components'];
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
        // return 1;

        // $results = $rets->GetObject('Property', 'Photo', '262287580', '*', 1);

        // var_dump($objects);
        // return $data[1];
        //     $results= $rets->Search('Property', 'RA_2', '*', ['Limit' => 3, 'Select' =>'L_ListingID']);
        //    dd($results); 
        //     return $results;
        // Rename returned number keys as names

        // $s = $results->toJSON();



        // get data by selecting column
        // $results= $rets->GetObject('Property', 'Photo', 'R2505889', '1', 1);
        // $results   = $rets->Search('Property', 'RA_2', '*', ['Limit' => 3, 'Select' => 'L_Area']);

        // $results= $rets->GetObject("Property", "Photo", 'R2497671', "*", 1);
        // foreach ($results as $photo) {
        //     $object_id = $photo->getObjectId();
        //     $url = $photo->getLocation();
        // }
        // $muid = "R2497671"; // put a real mUID number here

        // (L_Area=|1,VBD,VBE,VBN,VBS,F40,VCQ,VLD,F60,VMR,F80,F10,VNW,F20,VNV,VPI,VPQ,VPM,VRI,F50,VSQ,F30,VTW,VVE,VVW,VWV,VWH),
        // $results = $rets->Search('Property', 'RA_2',
        //     "(LV_vow_include=|Y),(L_Status=2_0),(L_Area=|2,4,21),(LM_Char10_11=|APTU,DUPXH,TWNHS)",
        //   ['Select' => 'L_ListingID,L_Area,L_AskingPrice,L_AddressNumber,L_AddressDirection,L_AddressStreet,L_City,L_State,L_Zip,L_ListAgent1,L_ListOffice1,L_ListAgent2,L_ListOffice2,L_ListAgent3,L_ListOffice3,L_ListingDate,L_OriginalPrice,L_Remarks,L_ClosingDate,L_SoldPrice','Limit'    =>    10]);
        // $results = $rets->Search('Property', 'RA_2',
        //     "(LV_vow_include=|Y),(L_Status=2_0),(L_Area=|2,4,21),(LM_Char10_11=|APTU,DUPXH,TWNHS)",
        //   ['Select' => 'L_ListingID,L_Area,L_Status','Limit'  =>    100]);
        //   ['Select' => 'L_ListingID,L_Area,L_AskingPrice,L_AddressNumber,L_AddressDirection,L_AddressStreet,L_City,L_State,L_Zip,L_ListAgent1,L_ListOffice1,L_ListAgent2,L_ListOffice2,L_ListAgent3,L_ListOffice3,L_ListingDate,L_OriginalPrice,L_Remarks,L_ClosingDate,L_SoldPrice','Limit'    =>    10]);
        // $results   = $rets->Search('Property',  'RD_1', "(L_Area=|29),(L_Status=1_0)", ['Select' => 'L_ListingID,L_Area,L_Status']);
        // $results   = $rets->Search('Property',  'RD_1', "(L_Area=|29),(L_Status=1_0),(L_Zip =|V2S 5K3)", ['Limit'  =>   5]);
        //     $objects = $rets->GetObject('Property', 'Photo', '262476044', '*', 1);
        //     $data = [];
        //     foreach ($objects as $photo) {
            //         $object_id = $photo->getObjectId();
            //         $url = $photo->getLocation();
            //         array_push($data, $url);
            //     }
            //     return sizeof($data);
        // $idd = Checker::first();
        $ofset = 0;
        // if($idd){
        //     $ofset = $idd['lastId'];
        // }
        $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)", ['Limit'  =>  2, 'Offset' => $ofset]);
        
        $alldata  = $results->toArray();
        $listingIds = [];
        foreach ($alldata as $value ) {
            array_push($listingIds,$value['L_ListingID']);

        }
        $listingData  = Listing::whereIn('listingID',$listingIds)->get(); ;
        return response()->json([
            'success' => false,
            'alldata' => $alldata,
            'listingData' => $listingData
        ], 422);
        \Log::info($alldata);
        return  1;
        
        
        foreach ($alldata as $key => $val) {

            //     $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 1);
            //     return $objects;
            // return $val;
                 $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $val['L_Address'])->getBody();;
                $json = json_decode($request);
                $lat = $json->results[0]->geometry->location->lat;
                $lng = $json->results[0]->geometry->location->lng;
                $val['lat'] = $lat;
                $val['lng'] = $lng;
            
            $jsonV=[];
            // $objects=[];
            if(sizeof($jsonV)>0 && !isset($jsonV['L_ListingID']) && ($jsonV['L_ListingID'] != $val['L_ListingID'])){

                $jsonV = JsonData::create(['data'=>$ss, 'L_ListingID'=>$val['L_ListingID']]);
            }
            $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 0);
                // return $objects->toJSON();
                
                $data = [];
            foreach ($objects as $ke => $photo) {
                    $url = $photo->getContent();
                    $name = time() . uniqid(rand()) . '.png';
                    $myFile = Storage::disk('spaces')->put($name, $url);
                    Storage::disk('spaces')->setVisibility($name, 'public');
                    $ll = Storage::disk('spaces')->url($name);
                    array_push($data, $url);
                    Picture::create(['filename'=> $ll, 'L_ListingID'=> $val['L_ListingID']]);
                    // return 1;
            }
            
            $ofset++;
            Checker::where('id', $idd['id'])->update(['lastId'=> $ofset]);
        }
        print "<pre>";

        // print_r($results->toJSON());
        print "</pre>";
        return "successfully insert data";
        }
        catch(\Exception $e){
            return $e;
        }
    }
    public function resorce2 (){
        $idd = UpdateChecker::first();
        if ($idd && $idd['status1'] == 'Running') return 1;
        UpdateChecker::where('id', $idd['id'])->update(['status1' => 'Running']);
        // try{
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

            $results   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0),(LM_Char10_11=|HOUSE)", ['Limit'  =>  100, 'Offset' => $ofset]);
            
            $alldata  = $results->toArray();
            $listingIds = [];

            foreach ($alldata as $value ) {
                array_push($listingIds,$value['L_ListingID']);
                $ofset++;

            }
            $listingData  = Listing::whereIn('listingID',$listingIds)->select('id','listingID','updateDate','lastPhotoUpdate')->get(); ;
            $check = UpdateChecker::where('id', $idd['id'])->update(['lastId' => $ofset, 'status1' => 'Stop']);
            $updateArray=[];
            foreach ($listingData as $value ) {
                $index = -1;
                // array_push($listingIds,$value['L_ListingID']);
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
                                    // 'db_value'=>$value,
                                    'rets_value'=>$alldata[$i]['L_UpdateDate'],
                                    // 'rets_value'=>$alldata[$i],
                                ];
                                $this->updateListingChanges($alldata[$i],$value);
                                array_push($updateArray,$ob);
        
                            }
                        }
    
                    }
                }
                if($index != -1){
                    unset($alldata[$index]);
                }
                $ofset++;

            }
            return response()->json([
                'success' => $check,
                'listingData' => $listingData,
                'updateArray' => $updateArray,
            ], 200);
        
        
        
        foreach ($alldata as $key => $val) {

            //     $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 1);
            //     return $objects;
            // return $val;
                 $client = new \GuzzleHttp\Client();
                $request = (string) $client->get('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyCPa98f4tcPyqDSgNEXilpho7LLcNjIJcs&address=' . $val['L_Address'])->getBody();;
                $json = json_decode($request);
                $lat = $json->results[0]->geometry->location->lat;
                $lng = $json->results[0]->geometry->location->lng;
                $val['lat'] = $lat;
                $val['lng'] = $lng;
            
            $jsonV=[];
            // $objects=[];
            if(sizeof($jsonV)>0 && !isset($jsonV['L_ListingID']) && ($jsonV['L_ListingID'] != $val['L_ListingID'])){

                $jsonV = JsonData::create(['data'=>$ss, 'L_ListingID'=>$val['L_ListingID']]);
            }
            $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 0);
                // return $objects->toJSON();
                
                $data = [];
            foreach ($objects as $ke => $photo) {
                    $url = $photo->getContent();
                    $name = time() . uniqid(rand()) . '.png';
                    $myFile = Storage::disk('spaces')->put($name, $url);
                    Storage::disk('spaces')->setVisibility($name, 'public');
                    $ll = Storage::disk('spaces')->url($name);
                    array_push($data, $url);
                    Picture::create(['filename'=> $ll, 'L_ListingID'=> $val['L_ListingID']]);
                    // return 1;
            }
            
            $ofset++;
            Checker::where('id', $idd['id'])->update(['lastId'=> $ofset]);
        }
        print "<pre>";

        // print_r($results->toJSON());
        print "</pre>";
        return "successfully insert data";
        // }
        // catch(\Exception $e){
        //     return $e;
        // }
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

    }

    public function testdelete(){
        
    }
}
