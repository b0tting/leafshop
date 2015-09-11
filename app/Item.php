<?php
/**
 * Created by PhpStorm.
 * User: motting
 * Date: 28-8-2015
 * Time: 12:01
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
class Item extends Model
{
    const SHIPPING = -1;
    protected $guarded = array();  // Important
}