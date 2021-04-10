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
            'json_data'
    ];
}
