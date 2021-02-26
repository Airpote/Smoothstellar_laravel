<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stellar extends Model
{
    protected $table = 'stellarcoin';

    protected $fillable = ['gateway_id','AssetID','AssetCode','val1','price','status','created_at','updated_at'];

}
