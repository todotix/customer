<?php 

namespace Todotix\Customer\App\Helpers;

use Validator;

class Customer {
    
    public static function after_seed_actions() {
      $menu = \Solunes\Master\App\Menu::where('permission','todotix')->first();
      if($menu){
        \Solunes\Master\App\Menu::create(['menu_type'=>'admin','level'=>2,'parent_id'=>$menu->id,'icon'=>'table','name'=>'Nómina de Clientes','permission'=>'todotix','link'=>'admin/model-list/customer?search=1']);
        \Solunes\Master\App\Menu::create(['menu_type'=>'admin','level'=>2,'parent_id'=>$menu->id,'icon'=>'table','name'=>'Nómina de Pagos','permission'=>'todotix','link'=>'admin/model-list/payment?search=1']);
      }
      \Solunes\Master\App\Menu::create(['menu_type'=>'admin','icon'=>'dollar','name'=>'Mis Pagos Pendientes','permission'=>'members','link'=>'admin/my-payments']);
      \Solunes\Master\App\Menu::create(['menu_type'=>'admin','icon'=>'table','name'=>'Mi Historial','permission'=>'members','link'=>'admin/my-history']);
    }

    public static function validateRegister($fields_array) {
        $response = [];
        foreach($fields_array as $item){
          if($item=='email'){
            $response[$item] = 'required|email';
          } else {
            $response[$item] = 'required';
          }
        }
        return $response;
    }

    public static function calculateAge($dateOfBirth, $dateNow = NULL) {
        if(!$dateNow){
            $dateNow = date("Y-m-d");
        }
        $diff = date_diff(date_create($dateOfBirth), date_create($dateNow));
        return $diff->format('%y');
    }

    public static function generateCustomer($ci_number, $email, $array, $password) {
        if(!$password){
        	$password = rand(100000,999999);
        }

        if(!$customer = \Todotix\Customer\App\Customer::where('ci_number', $ci_number)->where('email', $email)->first()){
            $customer = new \Todotix\Customer\App\Customer;
            $customer->ci_number = $ci_number;
            $customer->email = $email;
        }
        foreach($array as $key => $val){
            $customer->$key = $val;
        }
        if(config('customer.fields.age')){
        	$customer->age = \Customer::calculateAge($customer->birth_date);
        }
        $customer->save();
        if(config('customer.custom.register')){
            $customer = \CustomFunc::customerCustomRegister($customer);
        }
        return $customer;
    }
    
    public static function sendConfirmationEmail($main_customer) {
        $link = url('realizar-pago');
        \Mail::send('emails.notifications.succesful-register', ['link'=>$link, 'email'=>$main_customer->email, 'password'=>$main_customer->member_code], function($m) use($main_customer) {
          $m->to($main_customer->email, $main_customer->full_name)->subject('Copa Todotix 2018 | Su registro fue realizado correctamente');
        });
        return true;
    }
    
    public static function generateeCustomerPayment($customer, $name, $amount, $date, $item_type = NULL, $item_id = NULL) {
        $customer_payment = new \Solunes\Payments\App\Payment;
        $customer_payment->customer_id = $customer->id;
        $customer_payment->invoice = 0;
        $customer_payment->status = 'holding';
        $customer_payment->name = $name;
        $customer_payment->date = $date;
        $customer_payment->due_date = date( "Y-m-d", strtotime( $date." +1 month" ) );;
        //$customer_payment->amount = $event->amount;
        $customer_payment->save();
        $payment_item = new \Solunes\Payments\App\PaymentItem;
        $payment_item->parent_id = $customer_payment->id;
        $payment_item->name = $name;
        $payment_item->item_type = $item_type;
        $payment_item->item_id = $item_id;
        $payment_item->quantity = 1;
        $payment_item->detail = $name;
        $payment_item->price = $amount;
        $payment_item->save();
        return $customer_payment;
    }

    // Bridge: Encontrar cliente en sistema o devolver nulo
    public static function getCustomer($customer_id, $get_pending_payments = false, $for_api = false) {
        if($customer = \Todotix\Customer\App\Customer::where('id',$customer_id)->first()){
            $item = $customer->toArray();
            // Consultar y obtener los pagos pendientes del cliente en formato PagosTT: concepto, cantidad, costo_unitario
            $pending_payments = [];
            if($get_pending_payments&&config('pagostt.customer_all_payments')){
                foreach($customer->pending_payments as $payment){
                    if($for_api){
                        $pending_payments[$payment->id]['name'] = $payment->name;
                        $pending_payments[$payment->id]['due_date'] = $payment->due_date;
                        $pending_payments[$payment->id]['has_invoice'] = $payment->has_invoice;
                    }
                    $pending_payments[$payment->id]['amount'] = $payment->amount;
                    foreach($payment->payment_items as $payment_item){
                        $pending_payment = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $payment_item->amount, $payment->invoice);
                        $pending_payments[$payment->id]['items'][] = $pending_payment;
                    }
                }

            }
            $item['pending_payments'] = $pending_payments;
            return $item;
        } else {
            return NULL;
        }
    }

    // Bridge: Encontrar pago en sistema o devolver nulo
    public static function getPayment($payment_id) {
        if($payment = \Solunes\Payments\App\Payment::find($payment_id)){
            // Definir variables de pago en formato PagosTT: name, items[concepto, cantidad, costo_unitario]
            $item = [];
            $item['id'] = $payment->id;
            $item['name'] = $payment->name;
            $subitems_array = [];
            foreach($payment->payment_items as $payment_item){
                $subitems_array[] = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $payment_item->amount, $payment->invoice);
            }
            $item['amount'] = $payment->amount;
            $item['items'] = $subitems_array;
            return $item;
        } else {
            return NULL;
        }
    }

    // Bridge: Procesar pagos dentro del sistema luego de que la transacción fue procesada correctamente
    public static function transactionSuccesful($ptt_transaction) {
        $date = date('Y-m-d');
        if($ptt_transaction){
            foreach($ptt_transaction->ptt_transaction_payments as $ptt_transaction_payment){
                $payment_id = $ptt_transaction_payment->payment_id;
                $payment = \Solunes\Payments\App\Payment::find($payment_id);
                $payment->status = 'paid';
                $payment->paid_method = 'pagostt';
                $payment->transaction_payment_code = $ptt_transaction->payment_code;
                if($ptt_transaction->invoice_id){
                    $payment->invoice_id = $ptt_transaction->invoice_id;
                }
                $payment->payment_date = $date;
                $payment->save();
            }
            return true;
        } else {
            return false;
        }
    }

}