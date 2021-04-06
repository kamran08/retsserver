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
            $table->bigInteger('L_ListingID');
            $table->string('L_Type_', 191);
            $table->string('L_Area', 191);
            $table->string('L_Address', 191);
            $table->string('L_AddressDirection', 191);
            $table->string('L_AddressStreet', 191);
            $table->string('L_AddressUnit', 191);
            $table->string('LFD_Amenities_25', 191);
            $table->string('LFD_BasementArea_6', 191);
            $table->string('LM_char30_28', 191);
            $table->string('LV_vow_address', 191);
            $table->string('LFD_FeaturesIncluded_24', 191);
            $table->string('LM_Int1_2', 191);
            $table->string('LM_Dec_1', 191);
            $table->string('LM_Dec_7', 191);
            $table->string('LM_Dec_8', 191);
            $table->string('LM_Dec_9', 191);
            $table->string('LR_remarks33', 191);
            $table->date('L_ListingDate');
            $table->datetime('L_UpdateDate');
            $table->string('L_AddressNumber', 191);
            $table->string('L_City', 191);
            $table->string('L_State', 191);
            $table->string('L_Zip', 191);
            $table->integer('L_AskingPrice');
            $table->decimal('LM_Dec_16', 10, 2);
            $table->decimal('LM_Dec_12', 10, 2);
            $table->decimal('LM_Dec_14', 10, 2);
            $table->decimal('LM_Dec_11', 10, 2);
            $table->decimal('LM_Dec_13', 10, 2);
            $table->string('L_DisplayId', 191);
            $table->string('LM_Int1_1', 191);
            $table->integer('L_PictureCount');
            $table->datetime('L_Last_Photo_updt');
            $table->datetime('L_Status');
            $table->decimal('lat');
            $table->decimal('lang');
            $table->integer('LM_Int1_4');
            $table->integer('LM_Int1_7');
            $table->integer('LM_Int1_17');
            $table->integer('LM_Int1_18');
            $table->integer('LM_Int1_19');
            $table->integer('LM_Int2_2');
            $table->integer('LM_Int2_3');
            $table->integer('LM_Int2_5');
            $table->integer('LM_Int4_1');
            $table->integer('LM_Int1_8');
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
