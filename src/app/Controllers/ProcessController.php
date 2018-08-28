<?php

namespace Todotix\Customer\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Validator;
use Asset;
use AdminList;
use AdminItem;
use PDF;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProcessController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->prev = $url->previous();
	}

    public function postRegistro(Request $request) {
	    $fields_array = [];
        if(config('customer.fields.password')){
            $fields_array[] = 'password';
            $fields_array[] = 'password_confirmation';
        }
        if(config('customer.fields.member_code')){
            $fields_array[] = 'member_code';
        }
        if(config('customer.fields.shirt')){
            $fields_array[] = 'shirt';
        }
        if(config('customer.fields.shirt_size')){
            $fields_array[] = 'shirt_size';
        }
        if(config('customer.fields.invoice_data')){
            $fields_array[] = 'nit_name';
            $fields_array[] = 'nit_number';
        }
        if(config('customer.fields.emergency_long')){
            $fields_array[] = 'emergency_name';
            $fields_array[] = 'emergency_number';
        }
        $fields_array = array_merge($fields_array, ['ci_number','ci_expedition','first_name','last_name','email','phone','address','birth_date']);
	    $rules = \Customer::validateRegister($fields_array);
        if(config('customer.fields.password')){
	    	$rules['password'] = 'required|confirmed';
	    }
	    if(config('customer.custom.register_rules')){
	        $rules = \CustomFunc::customerCustomRegisterRules($rules);
	    }
	    $validator = \Validator::make($request->all(), $rules);
	    if(!$validator->fails()) {
	      $ci_number = $request->input('ci_number');
	      $email = $request->input('email');
	      $password = NULL;
          if(config('customer.fields.password')){
	        $password = $request->input('password');
	      }
	      /*if(\Todotix\Customer\App\Customer::where('ci_number', $ci_number)->first()){
	        return redirect($this->prev)->with('message_error', 'Ya existe un participante registrado con su carnet de identidad. Inicie sesión primero.')->withInput();
	      }*/
	      if(\Todotix\Customer\App\Customer::where('ci_number', $ci_number)->first()){
	        return redirect($this->prev)->with('message_error', 'Ya existe un participante registrado con su carnet de identidad. Inicie sesión primero.')->withInput();
	      }
	      $array = [];
	      foreach($fields_array as $key => $val){
	      	if(!in_array($val, ['password_confirmation','ci_number','email','age'])){
	        	$array[$val] = $request->input($val);
	      	}
	      }
	      $customer = \Customer::generateCustomer($ci_number, $email, $array, $password);
          if(config('customer.custom.after_register')){
            $customer = \CustomFunc::customerCustomAfterRegister($customer, $password);
          }
	      \Auth::login($customer->user);
	      \Customer::sendConfirmationEmail($customer);
	      return redirect('admin/finish-registration')->with('message_success', 'Felicidades, su registro fue realizado correctamente. Ahora finalice su registro y realice el pago para finalizar.');
	    } else {
	      return redirect($this->prev)->with(array('message_error' => 'Debe llenar todos los campos para finalizar'))->withErrors($validator)->withInput();
	    }
    }


    public function getCheckCi($ci_number) {
	    if($customer = \Todotix\Customer\App\Customer::where('ci_number', $ci_number)->first()){
	      // Send Mail
	      return ['exists'=>true, 'customer'=>$customer->toArray()];
	    } else {
	      return ['exists'=>false, 'customer'=>NULL];
	    }
    }

}