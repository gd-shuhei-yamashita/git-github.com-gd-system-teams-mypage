<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * ブランドテーブル
 */
class Brand extends ExModel
{
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    protected $table = 'brand';
    
    protected $primaryKey = ['id'];
    public $incrementing = false;
    
    protected $guarded = array();
    
    public $timestamps = true;

}
