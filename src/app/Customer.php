<?php

namespace Todotix\Customer\App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
	
	protected $table = 'customers';
	public $timestamps = true;

	/*if(config('customer.customer_trait')){
		use \App\Traits\Customer;
	}*/

	/* Creating rules */
	public static $rules_create = array(
		'user_id'=>'required',
		'first_name'=>'required',
		'last_name'=>'required',
		'full_name'=>'required',
        'ci_number'=>'required',
        'ci_expedition'=>'required',
		'member_code'=>'required',
		'email'=>'required',
        'phone'=>'required',
        'address'=>'required',
        'nit_number'=>'required',
        'nit_name'=>'required',
        'birth_date'=>'required',
        'active'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
        'user_id'=>'required',
        'first_name'=>'required',
        'last_name'=>'required',
        'full_name'=>'required',
        'ci_number'=>'required',
        'ci_expedition'=>'required',
        'member_code'=>'required',
        'email'=>'required',
        'phone'=>'required',
        'address'=>'required',
        'nit_number'=>'required',
        'nit_name'=>'required',
        'birth_date'=>'required',
        'active'=>'required',
	);
    
    public function user() {
        return $this->belongsTo('App\User');
    }

    public function customer_dependants() {
        return $this->hasMany('Solunes\Customer\App\CustomerDependant');
    }

    public function payments() {
        return $this->hasMany('Solunes\Payments\App\Payment');
    }

    public function pending_payments() {
        return $this->hasMany('Solunes\Payments\App\Payment')->where('status','holding');
    }

    public function paid_payments() {
        return $this->hasMany('Solunes\Payments\App\Payment')->where('status','paid');
    }

}