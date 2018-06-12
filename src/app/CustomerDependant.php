<?php

namespace Todotix\Customer\App;

use Illuminate\Database\Eloquent\Model;

class CustomerDependant extends Model {
	
	protected $table = 'customer_dependants';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'customer_id'=>'required',
		'name'=>'required',
		'active'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'customer_id'=>'required',
		'name'=>'required',
		'active'=>'required',
	);
    
    public function customer() {
        return $this->belongsTo('Solunes\Customer\App\Customer');
    }

}