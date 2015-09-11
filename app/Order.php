<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    const STATUS_NEW = 0;
    const STATUS_WAITING = 1;
    const STATUS_PAID = 2;
    const STATUS_SEND = 3;
    const STATUS_TYPES = [self::STATUS_NEW=>"New order", self::STATUS_PAID=>"Paypal paid", self::STATUS_WAITING=>"Waiting for bank payment", self::STATUS_SEND=>"Package send and done"];



    protected $guarded = array();  // Important

    public function orderItems()
    {
        return $this->hasMany('App\OrderItem');
    }

    public function delete() {
        // delete all related photos
    $this->orderItems()->delete();
    // as suggested by Dirk in comment,
    // it's an uglier alternative, but faster
    // Photo::where("user_id", $this->id)->delete()

    // delete the user
    return parent::delete();
    }
}