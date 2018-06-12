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

}