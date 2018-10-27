<?php 

namespace Todotix\Customer\App\Helpers;

use Validator;

class Customer {
    
    public static function before_seed_actions() {
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
        if(!$customer = \Todotix\Customer\App\Customer::where('ci_number', $ci_number)->first()){
            $customer = new \Todotix\Customer\App\Customer;
            $customer->ci_number = $ci_number;
            $customer->email = $email;
            $customer->active = 1;
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

    // Encontrar cliente en sistema o devolver nulo
    public static function getCustomer($customer_id, $get_pending_payments = false, $for_api = false, $custom_app_key = NULL) {
        if($customer = \Todotix\Customer\App\Customer::where('id',$customer_id)->first()){
            // Definir variables de cliente en formato PagosTT: email, name, nit_name, nit_number
            $array['id'] = $customer->id;
            $array['email'] = $customer->email;
            //$array['email'] = 'edumejia30@gmail.com';
            $array['ci_number'] = $customer->ci_number;
            $array['name'] = $customer->first_name.' '.$customer->last_name;
            $array['first_name'] = $customer->first_name;
            $array['last_name'] = $customer->last_name;
            $array['nit_name'] = $customer->nit_name;
            $array['nit_number'] = $customer->nit_number;
            // Consultar y obtener los pagos pendientes del cliente en formato PagosTT: concepto, cantidad, costo_unitario
            $pending_payments = [];
            $payment = NULL;
            if($get_pending_payments&&config('pagostt.customer_all_payments')){
                foreach($customer->pending_payments as $payment){
                    if($for_api){
                        $pending_payments[$payment->id]['name'] = $payment->name;
                        $pending_payments[$payment->id]['due_date'] = $payment->due_date;
                    }
                    if(config('customer.enable_test')==1){
                        $pending_payments[$payment->id]['amount'] = count($payment->payment_items);
                    } else {
                        $pending_payments[$payment->id]['amount'] = $payment->amount;
                    }
                    foreach($payment->payment_items as $payment_item){
                        if(config('customer.enable_test')==1){
                            $amount = 1;
                        } else {
                            $amount = $payment_item->amount;
                        }
                        $pending_payment = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $amount, $payment->invoice);
                        $pending_payments[$payment->id]['items'][] = $pending_payment;
                    }
                }
                if(!$payment){
                    return [];
                }
                $array['payment']['name'] = 'Múltiples Pagos';
                $array['payment']['has_invoice'] = $payment->invoice;
                //$array['payment']['metadata'][] = \Pagostt::generatePaymentMetadata('Tipo de Cambio', $payment->exchange);
            }
            $array['pending_payments'] = $pending_payments;
            return $array;
        } else {
            return NULL;
        }
    }

    // Encontrar pago en sistema o devolver nulo
    public static function getPayment($payment_id, $custom_app_key = NULL) {
        if($payment = \Solunes\Payments\App\Payment::where('id', $payment_id)->where('status','holding')->first()){
            // Definir variables de pago en formato PagosTT: name, items[concepto, cantidad, costo_unitario]
            $item = [];
            $item['id'] = $payment->id;
            $item['name'] = $payment->name;
            $subitems_array = [];
            foreach($payment->payment_items as $payment_item){
                if(config('customer.enable_test')==1){
                    $amount = 1;
                } else {
                    $amount = $payment_item->amount;
                }
                $subitems_array[] = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $amount, $payment->invoice);
            }
            if(config('customer.enable_test')==1){
                $item['amount'] = count($payment->payment_items);
            } else {
                $item['amount'] = $payment->amount;
            }
            $item['items'] = $subitems_array;
            $item['has_invoice'] = $payment->invoice;
            //$item['metadata'][] = \Pagostt::generatePaymentMetadata('Tipo de Cambio', $payment->exchange);
            return $item;
        } else {
            return NULL;
        }
    }

    // Encontrar seleccionados en un checkbox
    public static function getCheckboxPayments($customer_id, $payments_array, $custom_app_key) {
        \Log::info('getCheckboxPayments'.json_encode($payments_array));
        $payments = \Solunes\Payments\App\Payment::whereIn('id', $payments_array)->get();
        if(count($payments)>0){
            $items = [];
            foreach($payments as $payment){
                // Definir variables de pago en formato PagosTT: name, items[concepto, cantidad, costo_unitario]
                $item = [];
                $item['id'] = $payment->id;
                $item['name'] = $payment->name;
                $subitems_array = [];
                foreach($payment->payment_items as $payment_item){
                    if(config('customer.enable_test')==1){
                        $amount = 1;
                    } else {
                        $amount = $payment_item->amount;
                    }
                    $subitems_array[] = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $amount, $payment->invoice);
                }
                if(config('services.enable_test')==1){
                    $item['amount'] = count($payment->payment_items);
                } else {
                    $item['amount'] = $payment->amount;
                }
                $item['items'] = $subitems_array;
                $items[$payment->id] = $item;
            }
            $array['pending_payments'] = $items;
            $array['payment']['name'] = 'Múltiples pagos seleccionados';
            $array['payment']['has_invoice'] = $payment->invoice;
            //$array['payment']['metadata'][] = \Pagostt::generatePaymentMetadata('Tipo de Cambio', $payment->exchange);
            return $array;
        } else {
            return NULL;
        }
    }

    // Bridge: Procesar pagos dentro del sistema luego de que la transacción fue procesada correctamente
    public static function transactionSuccesful($transaction) {
        $date = date('Y-m-d');
        if($transaction&&$transaction->status=='paid'){
            foreach($transaction->transaction_payments as $transaction_payment){
                $transaction_payment->processed = 1;
                $transaction_payment->save();
                $payment = $transaction_payment->payment;
                if($transaction_invoice = $transaction->transaction_invoice){
                    $payment->invoice = 1;
                    $payment->invoice_name = $transaction_invoice->customer_name;
                    $payment->invoice_nit = $transaction_invoice->customer_nit;
                    $payment->invoice_url = $transaction_invoice->invoice_url;
                }
                $payment->status = 'paid';
                $payment->payment_date = $date;
                $payment->save();
            }
            return true;
        } else {
            return false;
        }
    }

}