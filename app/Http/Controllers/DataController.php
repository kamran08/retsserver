<?php

namespace App\Http\Controllers;
date_default_timezone_set('America/New_York');

use Illuminate\Http\Request;
use App\Picture;
use App\JsonData;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use File;
use Image;
class DataController extends Controller
{
    public function uploadThumb(Request $request)
    {
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





    public function uploadThumb1(Request $request)
    {
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

    public function show(Image $image)
    {
        return $image->url;
    }

    //
    public function getData(Request $request){
        try{

        }catch(\Exception $e){
            
        }


        set_time_limit(2000000);
        $config = new \PHRETS\Configuration;
        // $config = \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
        $config->setLoginUrl('http://reb.retsiq.com/contactres/rets/login')
            ->setUsername('RETSARVING')
            ->setPassword('wjq6PJqUA45EGU8')
            ->setRetsVersion('1.7.2');
        \PHRETS\Http\Client::set(new \GuzzleHttp\Client);
        $rets = new \PHRETS\Session($config);
        $connect = $rets->Login();
        $resource = 'Property';
        $photo_resource_type = 'Property';

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
        $id = 0;
        $idd = JsonData::orderBy('id', 'DESC')->first();
        if($idd){
            $id = $idd['L_ListingID'];
        }
        // return $id;
        $ofset = 4;
        $results   = $rets->Search('Property',  'RD_1', "(L_Area=|29),(L_Status=1_0)" ,['Limit'  =>   5, 'Offset'=>$ofset,'select' => 'L_ListingID']);
        $alldata  = $results->toArray();
        // foreach ($alldata as $key => $val) {
        //     $ss = json_encode($val);
        //     JsonData::create(['data'=>$ss, 'L_ListingID'=>$val['L_ListingID']]);

        //     $objects = $rets->GetObject('Property', 'Photo', $val['L_ListingID'], '*', 1);
        //     $data = [];
        //     foreach ($objects as $photo) {
        //         $object_id = $photo->getObjectId();
        //         $url = $photo->getLocation();
        //         array_push($data, $url);
        //     }
        //     foreach ($data as $k => $v) {
        //         Picture::create(['filename'=> $v, 'L_ListingID'=> $val['L_ListingID']]);
        //     }
            
        // }
        
        // $results   = $rets->Search('Property',  'RD_1', "(L_ListingID =|2549233)", ['Limit'  =>   1]);

        // $results   = $rets->Search('Property', 'RA_2', '*', ['Limit' => 2, 'Select' => 'L_ListingID,L_Area,L_Status']);
        // $objects = $rets->GetObject('Property', 'Photo', '262568608', '1');
        // $objects->first();
        // $objects->last();
        // $objects = $objects->slice(0, 10);
        // foreach ($results as $record) {
        //     print "<pre>";
        //     // print_r($objects->toJSON());
        //     print_r($record['Address']);
        //     // print_r($objects->toJSON());
        //     print_r($record->get('Address'));
        //     print "</pre>";
        //     // is the same as:
        //     echo "hello";
        // }
        // foreach ($results as $record) {
        //     print "<pre>";
        //     print_r( $record->toJson());
        //     print "</pre>";
        // }
        // return 1;
        print "<pre>";
        print_r($results->toJSON());
        // print_r($objects->toJSON());
        print "</pre>";
        return sizeof($results->toArray());
        // $all_ids=  $results->lists('L_Area');


        // $objects = $rets->GetObject($rets_resource, $object_type, $object_keys);

        // // grab the first object of the set
        // $objects->first();

        // // grab the last object of the set
        // $objects->last();

        // // throw out everything but the first 10 objects
        // $objects = $objects->slice(0, 10);


        // $results = $rets->GetObject('Property', 'Photo', '262363537','*',1);
        return 1;
        $a = [];
        $results = $rets->GetObject('Property', 'Photo', '262287580','*',1);
        foreach ($results as $result){
            $a[]= $result->getLocation();
            // dd($result);
        }
        // dd($results);
        $s = $results->toJSON();
        return $a;
       

      
       
    }
}
