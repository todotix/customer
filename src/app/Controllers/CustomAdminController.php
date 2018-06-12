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

class CustomAdminController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
      $this->middleware('auth');
      //$this->middleware('permission:dashboard');
	  $this->prev = $url->previous();
      $this->module = 'custom-admin';
	}
		
	public function getRedirect() {
		if(!auth()->check()){
			return redirect('auth/login')->with('message_error','Debe iniciar sesión.');
		}
		$user = auth()->user();
		if(!$user->customer){
			return redirect('admin/model-list/payments')->with('message_success','Inició sesión como administrador.');
		}
		return redirect('admin/my-payments')->with('message_success','Inició sesión correctamente, a continuación sus pagos pendientes.');
	}

	public function getMyAccounts() {
		$user = auth()->user();
		$customers = $user->customers;
		$array = ['customers'=>$customers];
		return view('content.my-accounts', $array);
	}

	public function postEditPassword(Request $request) {
		$user = auth()->user();
		if($user->customer){
			$customer = $user->customer;
			$rules = \App\Customer::$rules_password;
	        $validator = \Validator::make($request->all(), $rules);
	        if($validator->passes()) {
	        	$customer->member_code = $request->input('member_code');
	        	$customer->save();
	        	$user = $customer->user;
	        	$user->password = $request->input('member_code');
	        	$user->save();
	        	return redirect($this->prev)->with('message_success', 'Su contraseña fue editada correctamente.');
			} else {
				return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos.')->withInput();
			}
		} else {
			return redirect($this->prev)->with('message_error', 'No tiene una cuenta asociada.');
		}
	}

	public function getMyAccount($customer_id = NULL, $action = 'edit', $dependant_id = NULL) {
		$user = auth()->user();
		$dependants = false;
		if($customer = $user->customers()->where('id',$customer_id)->first()){
			if($action=='create'){
				$dependants = true;
			} else if($dependant_id&&$customer = $customer->dependants()->where('id',$dependant_id)->first()){
				$dependants = true;
			}
		} else {
			$customer = NULL;
			return redirect('admin')->with('message_error', 'Su cuenta no tiene un cliente asociado.');
		}
		if($dependants){
			$custom_rules = false;
		} else {
			$custom_rules = true;
		}
		$expeditions = ['LP'=>'LP','SC'=>'SC','CB'=>'CB','CH'=>'CH','PO'=>'PO','OR'=>'OR','TA'=>'TA','BE'=>'BE','PA'=>'PA','EXTRANJERO'=>'EXTRANJERO'];
		$array = ['parent_customer_id'=>$customer_id,'customer'=>$customer,'dependants'=>$dependants,'custom_rules'=>$custom_rules,'action'=>$action,'expeditions'=>$expeditions];
		return view('content.my-account', $array);
	}

	public function postEditAccount(Request $request) {
		$user = auth()->user();
		if($request->has('parent_customer_id')&&$customer = $user->customers()->where('id', $request->input('parent_customer_id'))->first()){
			if($customer->id==$request->input('customer_id')){
				$customer = $user->customer;
				$dependant = false;
			} else if($customer->dependants()->where('id',$request->input('customer_id'))->first()){
				$customer = $customer->dependants()->where('id',$request->input('customer_id'))->first();
				$dependant = true;
			} else {
				return redirect($this->prev)->with('message_error', 'Hubo un error al procesar el formulario.');
			}
	        $action = $request->input('action');
	        if($action=='create'){
				$dependant = true;
	        }
			$rules = \App\Customer::$rules_send;
	        $validator = \Validator::make($request->all(), $rules);
	        $customer = \App\Customer::find($request->input('customer_id'));
	        if($validator->passes()&&$action&&$customer) {
	        	if($action=='create'){
	        		$parent_customer = $customer;
	        		$last_dependant = $parent_customer->dependants()->orderBy('id','DESC')->first();
	        		if(!$last_dependant){
	        			$type = 'DEPEND 1';
	        		} else {
	        			$explode = explode(' ', $last_dependant->type);
	        			$type = 'DEPEND '.(intval($explode[1])+1);
	        			//print_r($type);
	        		}
	        		$customer = new \App\Customer;
	        		$customer->type = $type;
	        		$customer->code = $parent_customer->code;
	        		$customer->member_code = 12345678;
	        		$customer->category_id = $parent_customer->category_id;
	        	}
	        	$customer->first_name = mb_strtoupper($request->input('first_name'), 'UTF-8');
	        	$customer->last_name = mb_strtoupper($request->input('last_name'), 'UTF-8');
	        	$customer->last_name_2 = mb_strtoupper($request->input('last_name_2'), 'UTF-8');
	        	$customer->email = $request->input('email');
	        	$customer->phone = $request->input('phone');
	        	$customer->cellphone = $request->input('cellphone');
	        	if($dependant){
		        	$customer->ci_number = $request->input('ci_number');
		        	$customer->ci_expedition = $request->input('ci_expedition');
	        	} else {
		        	$customer->nit_number = $request->input('nit_number');
		        	$customer->nit_name = $request->input('nit_name');
	        	}
	        	$customer->birth_date = $request->input('birth_date');
	        	$customer->career = $request->input('career');
	        	$customer->office_phone = $request->input('office_phone');
	        	$customer->office_address = $request->input('office_address');
	        	$full_name = $customer->first_name;
	        	if($customer->last_name){
	        		$full_name .= ' '.$customer->last_name;
	        	}
	        	if($customer->last_name_2){
	        		$full_name .= ' '.$customer->last_name_2;
	        	}
	        	$customer->full_name = $full_name;
	        	$customer->save();
	        	if($customer->user){
		        	$user = $customer->user;
		        	$user->name = $full_name;
		        	$user->save();
	        	}
	        	if($action=='create'){
	        		$url = url('admin/model/customer/edit/'.$customer->id.'/es');
	        		$message = 'El usuario '.$user->name.' registró un dependiente que debe ser aprobado. Click para activar.';
	        		\FuncNode::make_dashboard_notitification($message, [1,2], $url, $message);
	        		$message = 'Su cuenta de dependiente fue creada correctamente, sin embargo debe ser aprobada primero.';
	        	} else {
	        		$message = 'Su cuenta fue editada correctamente.';
	        	}
	        	return redirect($this->prev)->with('message_success', $message);
			} else {
				return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos.')->withInput();
			}
		} else {
			return redirect($this->prev)->with('message_error', 'No tiene una cuenta asociada.');
		}
	}

	public function getMyDependants() {
		$user = auth()->user();
		$customers = $user->customers;
		$array = ['customers'=>$customers];
		return view('content.my-dependants', $array);
	}
	
	public function getManualPayment($id) {
		if($item = \Solunes\Payments\App\Payment::find($id)){
			$item->transaction_payment_code = \Pagostt::generatePaymentCode();
			$item->payment_date = date('Y-m-d');
			$item->status = 'paid';
			$item->paid_method = 'manual';
			$item->save();
			return redirect($this->prev)->with('message_success', 'Pago realizado correctamente.');
		}
		return redirect($this->prev)->with('message_error', 'Error al realizar el pago.');
	}

	public function getManualLogin($customer_id) {
		if($item = \Todotix\Customer\App\Customer::find($customer_id)){
			auth()->login($item->user);
			return redirect('my-payments')->with('message_success', 'Sesión cambiada correctamente.');
		}
		return redirect($this->prev)->with('message_success', 'Hubo un error al cambiar su sesión.');
	}

	public function getCustomerList() {
		$array['items'] = \Todotix\Customer\App\Customer::get();
		return view('customer::content.customer-lists', $array);
	}
		
	public function getPaymentsList() {
		$items = \Solunes\Master\App\Payment::whereNotNull('id');
		if(request()->has('search')){
			if(request()->has('f_team_id')&&request()->input('f_team_id')!=0&&$team = \App\Team::find(request()->input('f_team_id'))){
				$customer_ids = $team->team_customers()->lists('customer_id')->toArray();
				$items = $items->whereIn('customer_id', $customer_ids);
			}
			if(request()->has('f_status')){
				$items = $items->where('status', request()->input('f_status'));
			}
			if(request()->has('f_from')&&request()->input('f_from')!=NULL){
				$items = $items->where('date', '>=', request()->input('f_from'));
			}
			if(request()->has('f_to')&&request()->input('f_to')!=0){
				$items = $items->where('date', '<=', request()->input('f_to'));
			}
		}
		$items = $items->with('customer','customer.team_customer')->get();
		$array = ['dt'=>'create'];
		$array['items'] = $items;
		$array['f_teams'] = \App\Team::lists('name','id')->toArray();
		$array['f_status'] = ['pending'=>'Pendiente','paid'=>'Pagado'];
		return view('customer::content.payments-lists', $array);
	}

	public function getMyPayments() {
		$array['items'] = [];
		$user = auth()->user();
		if(count($user->customers)>0){
			foreach($user->customers as $customer){
				$array['items'][] = ['customer'=>$customer,'payments'=>$customer->pending_payments];
			}
		}
		return view('customer::content.my-payments', $array);
	}
	
	public function postMakePayment(Request $request) {
	    if($request->has('name')&&$request->has('last_name')&&$request->has('email')){

	      $participant = new \App\Participant;
	      $participant->name = $request->input('name');
	      $participant->last_name = $request->input('last_name');
	      $participant->email = $request->input('email');
	      $participant->status = 'holding';
	      $participant->save();

	      return redirect($this->prev)->with('message_success', '"'.$participant->name.' '.$participant->last_name.'" fue registrado correctamente con el correo "'.$participant->email.'".');
	    } else {
	      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos para registrar el participante.');
	    }
	}
	
	public function getMyHistory() {
		$array['items'] = [];
		$user = auth()->user();
		if(count($user->customers)>0){
			foreach($user->customers as $customer){
				$array['items'][] = ['customer'=>$customer,'payments'=>$customer->paid_payments];
			}
		}
		return view('customer::content.my-history', $array);
	}
	
}