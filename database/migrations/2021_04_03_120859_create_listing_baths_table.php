<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingBathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_baths', function (Blueprint $table) {
            $table->bigIncrements('id');
            // room
            // $table->string('LM_char5_8', 191);
            // $table->string('LM_char5_9', 191);
            // $table->string('LM_char5_7', 191);
            // $table->string('LM_Char10_25', 191);
            // $table->string('LM_char5_44', 191);
            // $table->string('LM_char5_45', 191);
            // $table->string('LM_char5_42', 191);
            // $table->string('LM_char5_43', 191);
            // $table->string('LM_char5_48', 191);
            // $table->string('LM_char5_49', 191);
            // $table->string('LM_char5_46', 191);
            // $table->string('LM_char5_47', 191);
            // $table->string('LM_char5_52', 191);
            // $table->string('LM_char5_53', 191);
            // $table->string('LM_char5_50', 191);
            // $table->string('LM_char5_51', 191);
            // $table->string('LM_char5_56', 191);
            // $table->string('LM_char5_57', 191);
            // $table->string('LM_char5_54', 191);
            // $table->string('LM_char5_55', 191);
            // $table->string('LM_char5_60', 191);
            // $table->string('LM_Char10_26', 191);
            // $table->string('LM_char5_58', 191);
            // $table->string('LM_char5_59', 191);
            // $table->string('LM_Char10_29', 191);
            // $table->string('LM_Char10_30', 191);
            // $table->string('LM_Char10_27', 191);
            // $table->string('LM_Char10_28', 191);
            // $table->string('LM_char10_33', 191);
            // $table->string('LM_char10_34', 191);
            // $table->string('LM_char10_31', 191);
            // $table->string('LM_char10_32', 191);
            // $table->string('LM_char10_37', 191);
            // $table->string('LM_char10_38', 191);
            // $table->string('LM_char10_35', 191);
            // $table->string('LM_char10_36', 191);
            // $table->string('LM_char10_41', 191);
            // $table->string('LM_char10_42', 191);
            // $table->string('LM_char10_39', 191);
            // $table->string('LM_char10_40', 191);
            // $table->string('LM_char10_45', 191);
            // $table->string('LM_char10_46', 191);
            // $table->string('LM_char10_43', 191);
            // $table->string('LM_char10_44', 191);
            // $table->string('LM_char5_12', 191);
            // $table->string('LM_char5_13', 191);
            // $table->string('LM_char5_10', 191);
            // $table->string('LM_char5_11', 191);
            // $table->string('LM_char10_49', 191);
            // $table->string('LM_char10_50', 191);
            // $table->string('LM_char10_47', 191);
            // $table->string('LM_char10_48', 191);
            // $table->string('LM_char10_53', 191);
            // $table->string('LM_char10_54', 191);
            // $table->string('LM_char10_51', 191);
            // $table->string('LM_char10_52', 191);
            // $table->string('LM_char10_57', 191);
            // $table->string('LM_char10_58', 191);
            // $table->string('LM_char10_55', 191);
            // $table->string('LM_char10_56', 191);
            // $table->string('LM_char10_61', 191);
            // $table->string('LM_char10_62', 191);
            // $table->string('LM_char10_59', 191);
            // $table->string('LM_char10_60', 191);
            // $table->string('LM_char10_65', 191);
            // $table->string('LM_char10_66', 191);
            // $table->string('LM_char10_63', 191);
            // $table->string('LM_char10_64', 191);
            // $table->string('LM_char10_69', 191);
            // $table->string('LM_char10_70', 191);
            // $table->string('LM_char10_67', 191);
            // $table->string('LM_char10_68', 191);
            // $table->string('LM_Char25_6', 191);
            // $table->string('LM_Char25_7', 191);
            // $table->string('LM_Char25_4', 191);
            // $table->string('LM_Char25_5', 191);
            // $table->string('LM_Char25_10', 191);
            // $table->string('LM_Char25_11', 191);
            // $table->string('LM_Char25_8', 191);
            // $table->string('LM_Char25_9', 191);
            // $table->string('LM_Char25_14', 191);
            // $table->string('LM_Char25_15', 191);
            // $table->string('LM_Char25_12', 191);
            // $table->string('LM_Char25_13', 191);
            // $table->string('LM_char5_16', 191);
            // $table->string('LM_char5_17', 191);
            // $table->string('LM_char5_14', 191);
            // $table->string('LM_char5_15', 191);
            // $table->string('LM_char5_20', 191);
            // $table->string('LM_char5_21', 191);
            // $table->string('LM_char5_18', 191);
            // $table->string('LM_char5_19', 191);
            // $table->string('LM_char5_24', 191);
            // $table->string('LM_char5_25', 191);
            // $table->string('LM_char5_22', 191);
            // $table->string('LM_char5_23', 191);
            // $table->string('LM_char5_28', 191);
            // $table->string('LM_char5_29', 191);
            // $table->string('LM_char5_26', 191);
            // $table->string('LM_char5_27', 191);
            // $table->string('LM_char5_32', 191);
            // $table->string('LM_char5_33', 191);
            // $table->string('LM_char5_30', 191);
            // $table->string('LM_char5_31', 191);
            // $table->string('LM_char5_36', 191);
            // $table->string('LM_char5_37', 191);
            // $table->string('LM_char5_34', 191);
            // $table->string('LM_char5_35', 191);
            // $table->string('LM_char5_40', 191);
            // $table->string('LM_char5_41', 191);
            // $table->string('LM_char5_38', 191);
            // $table->string('LM_char5_39', 191);
            // // room

            // $table->string('LM_char10_74', 191);
            // $table->string('LM_Int1_20', 191);
            // $table->string('LM_Int1_9', 191);
            // $table->string('LM_Char1_1', 191);
            // $table->string('LM_Char25_16', 191);
            // $table->string('LM_char1_32', 191);
            // $table->string('LM_char10_72', 191);
            // $table->string('LM_Int4_11', 191);
            // $table->string('LM_char1_33', 191);
            // $table->string('LM_char10_73', 191);
            // $table->string('LM_Int4_12', 191);
            // $table->string('LM_char1_34', 191);
            // $table->string('LM_Int4_13', 191);
            // $table->string('LM_Int1_10', 191);
            // $table->string('LM_Char1_2', 191);
            // $table->string('LM_Char25_17', 191);
            // $table->string('LM_Int1_11', 191);
            // $table->string('LM_Char1_3', 191);
            // $table->string('LM_Char25_18', 191);
            // $table->string('LM_Int1_12', 191);
            // $table->string('LM_Char1_4', 191);
            // $table->string('LM_Char25_19', 191);
            // $table->string('LM_Int1_13', 191);
            // $table->string('LM_Char1_5', 191);
            // $table->string('LM_Char25_20', 191);
            // $table->string('LM_Int1_14', 191);
            // $table->string('LM_Char1_6', 191);
            // $table->string('LM_Char25_21', 191);
            // $table->string('LM_Int1_15', 191);
            // $table->string('LM_Char1_7', 191);
            // $table->string('LM_Char25_22', 191);
            // $table->string('LM_Int1_16', 191);
            // $table->string('LM_Char1_8', 191);
            // $table->string('LM_Char25_23', 191);
            // $table->string('LM_Int4_10', 191);
            // $table->string('LM_char1_31', 191);
            // $table->string('LM_char10_71', 191);
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
        Schema::dropIfExists('listing_baths');
    }
}
