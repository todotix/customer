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
		//'member_code'=>'required',
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
        //'member_code'=>'required',
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

    // DEL FUTBOL CTLP

    public function total_goals() {
        return $this->hasMany('App\TotalGoal');
    }

    public function getGoalsAttribute() {
        if($total_goals = $this->total_goals()->orderBy('id','DESC')->first()){
            return $total_goals->goals;
        }
        return 0;
    }

    public function cards() {
        return $this->hasMany('App\Card');
    }

    public function yellow_cards() {
        return $this->hasMany('App\Card')->where('yellow_card','>',0);
    }

    public function red_cards() {
        return $this->hasMany('App\Card')->where('red_card','>',0);
    }

    public function team_customer() {
        return $this->hasOne('App\TeamCustomer');
    }

    public function team_customers() {
        return $this->hasMany('App\TeamCustomer');
    }

}