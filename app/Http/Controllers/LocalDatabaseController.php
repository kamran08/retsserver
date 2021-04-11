<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class LocalDatabaseController extends Controller
{
    //
    public function storeDataFromDataServer(Request $request){
        try {
             $data = $request->all();
            if(isset($data['id']))  unset($data['id']);
            if(isset($data['thumbnail']))  unset($data['thumbnail']);
            if(isset($data['images']))  unset($data['images']);
            unset($data['created_at']);
            unset($data['updated_at']);

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
        return 'hello';
        try {
        $data = $request->all();
        $ch = Listing::where('listingID', $data['listingID'])->first();
        if($ch){
            return Listing::where('listingID', $data['listingID'])->update($data);
        }
        else{
             return  Listing::create($data);
        }
        } catch (\Exception $e) {
            \Log::info($e);
            return false;
        }
    }
}
