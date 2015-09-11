<?php
/**
 * Created by PhpStorm.
 * User: motting
 * Date: 28-8-2015
 * Time: 12:03
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = array();  // Important

    public function item() {
        return $this->belongsTo('App\Item');
    }
}