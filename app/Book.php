<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
   use SoftDeletes;
   protected $dates = ['created_at', 'updated_at', 'deleted_at'];

   protected $fillable = ['purchase_order_id','description_sub_category_id','units_id','rate','qty','material_amount','hsn','remarks','mri_material_id'];

}
