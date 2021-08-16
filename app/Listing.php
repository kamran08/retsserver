<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    //
    protected $fillable = [
            'listingID',
            'class',
            'completed',
             'thumbnail',
              'images',
            'listingType',
            'listingArea',
            'listingArea',
            'listingAddress',
            'listingAddressDirection',
            'listingAddressStreet',
            'listingAddressUnit',
            'amenities' ,
            'basementArea',
            'lotSizeLenth',
            'onInternet',
            'features',
            'fireplaces',
            'floorAreaTotal',
            'lotSizeWidthFeet',
            'lotSizeMeter',
            'internetRemarks',
            'listingDate',
            'updateDate',
            'addressNumber',
            'city',
            'subArea',
            'state',
            'zip',
            'askingPrice',
            'grossTaxes',
            'lotSizeArea',
            'lotSizeAreaSqMt',
            'lotSizeAreaSqFt',
            'displayId',
            'floorLevel',
            'pictureCount',
            'lastPhotoUpdate',
            'status',
            'houseType',
            'lat',
            'lang',
            'totalBedrooms',
            'totalRooms',
            'halfBaths',
            'fullBaths',
            'totalBaths',
            'age',
            'yearBuilt',
            'texPerYear',
            'unitsInDevelopment',
            'kitchens',
            'json_data',
            'perSqrtPrice',
            'organizationName1',
            'organizationName2',
            'startaFee',
            'originalListPrice',
            'soldPrice',
            'soldPrice',
            'previousPrice',
            'isSent',
            'soldPricePerSqrt'
    ];
  public function missed()
  {
    return $this->hasMany('App\MapMissingRequest', 'list_id');
  }
}
