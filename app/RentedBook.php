<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentedBook extends Model
{
    use SoftDeletes;
   	protected $dates = ['created_at', 'updated_at', 'deleted_at'];
   	protected $fillable = ['users_id','books_id','books_issued_date','books_returned_date'];


}
