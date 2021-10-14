<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

class LocalDatabaseController extends Controller
{
    

    public function checkNullClass(Request $request){
        
        $alldata = Listing::select('listingID', 'id','displayId')->whereNull('class')->limit(1)->get();


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
            foreach($alldata as $item){
                $results1   = $rets->Search('Property',  'RA_2', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|APTU,DUPXH,TWNHS), (L_DisplayId=".$item['displayId'].")",['select'=>'L_DisplayId,L_Status']);
                $data1= $results1->toArray();
                // return $data1[0]['L_Status'];


                if(sizeof($data1)>0){
                    $ob = [
                        'class' =>'RA_2',
                        'displayId'=>$item['displayId'],
                        'status'=>$data1[0]['L_Status']
                    ];
                    $this->send_data($ob);
                    continue;
                }
               
                $results2   = $rets->Search('Property',  'RD_1', "(L_Status=1_0,2_0,5_1),(LM_Char10_11=|HOUSE),(L_DisplayId=".$item['displayId'].")",['select'=>'L_DisplayId,L_Status']);
                $data2= $results2->toArray();
                if(sizeof($data2)>0){
                // return $data2[0]['L_Status'];

                    $ob = [
                        'class' =>'RD_1',
                        'displayId'=>$item['displayId'],
                        'status'=>$data2[0]['L_Status']
                    ];
                    $this->send_data($ob);
                }
            }
            return "suess";
    }

    public function send_data($data){
        if($data['status']=='Terminated'){
                  Listing::whereNull('class')->where('displayId',$data['displayId'])->delete();

        }
        else{

            Listing::whereNull('class')->where('displayId',$data['displayId'])->update($data);
        }
        
        
        $l = json_decode(json_encode($data), true);
        
        try{
            $client2 = new \GuzzleHttp\Client();
            $request2 = (string) $client2->post('https://m.youhome.cc/updateDataFromDataServer', ['form_params' => $l])->getBody();
            $json2 = json_decode($request2);
          //   return  $request2;

      } catch (\Exception $e) {
          \Log::info($e);
          return false;
      }

    }
}
