<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->bigIncrements('id');
            // L_ListingID
            $table->bigInteger('listingID');
            // L_Type_
            $table->string('listingType', 191);
            $table->integer('completed');
            $table->string('class', 191);
            $table->string('thumbnail', 191);
            $table->text('images');
            // L_Area
            $table->string('listingArea', 191);
            // L_Address
            $table->string('listingAddress', 191);
            // L_AddressDirection
            $table->string('listingAddressDirection', 191);
            // L_AddressStreet
            $table->string('listingAddressStreet', 191);
            // L_AddressUnit
            $table->string('listingAddressUnit', 191);
            // LFD_Amenities_25
            $table->string('amenities', 191);
            // LFD_BasementArea_6
            $table->string('basementArea', 191);
            // LM_char30_28
            $table->string('lotSizeLenth', 191);
            // LV_vow_address
            $table->string('onInternet', 191);
            // LFD_FeaturesIncluded_24
            $table->string('features', 191);
            // LM_Int1_2
            $table->string('fireplaces', 191);
            // LM_Dec_1
            $table->decimal('floorArea', 10, 2);
            // LM_Dec_7
            $table->decimal('floorAreaTotal', 10, 2);
            // LM_Dec_8
            $table->decimal('lotSizeWidthFeet', 10, 2);
            // LM_Dec_9
            $table->decimal('lotSizeMeter', 10, 2);
            // LR_remarks33
            $table->string('internetRemarks', 191);
            // L_ListingDate
            $table->date('listingDate');
            // L_UpdateDate
            $table->datetime('updateDate');
            // L_AddressNumber
            $table->string('addressNumber', 191);
            // L_City
            $table->string('city', 191);
            // LM_Char10_5
            $table->string('subArea', 191);
            // L_State
            $table->string('state', 191);
            // L_Zip
            $table->string('zip', 191);
            // L_AskingPrice
            $table->integer('askingPrice');
            // LM_Dec_16
            $table->decimal('grossTaxes', 10, 2);
            // LM_Dec_12
            $table->decimal('lotSizeArea', 10, 2);
            // LM_Dec_13
            $table->decimal('lotSizeAreaSqMt', 10, 2);
            // LM_Dec_11
            $table->decimal('lotSizeAreaSqFt', 10, 2);
            // L_DisplayId
            $table->string('displayId', 191);
            // LM_Int1_1
            $table->integer('floorLevel');
            // L_PictureCount
            $table->integer('pictureCount');
            // L_Last_Photo_updt
            $table->datetime('lastPhotoUpdate');
            // L_Status
            $table->string('status',191);
            // LM_Char10_11
            $table->string('houseType', 191);
            $table->decimal('lat');
            $table->decimal('lang');
            // LM_Int1_4
            $table->integer('totalBedrooms');
            // LM_Int1_7
            $table->integer('totalRooms');
            // LM_Int1_17
            $table->integer('halfBaths');
            // LM_Int1_18
            $table->integer('fullBaths');
            // LM_Int1_19
            $table->integer('totalBaths');
            // LM_Int2_2
            $table->integer('yearBuilt');
            // LM_Int2_3
            $table->integer('age');
            // LM_Int2_5
            $table->decimal('texPerYear', 10, 2);
            // LM_Int4_1
            $table->integer('unitsInDevelopment');
            // LM_Int1_8
            $table->integer('kitchens');
            $table->text('json_data');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listings');
    }
}
