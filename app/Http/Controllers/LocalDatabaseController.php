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
        unset($data['created_at']);
        unset($data['updated_at']);
        $d = Listing::create($data);
        return $d;
        } catch (\Exception $e) {
            return false;
        }
    }
}
