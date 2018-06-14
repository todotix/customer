<?php

namespace Todotix\Customer\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class MasterSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Módulo General de Empresa ERP
        $node_customer = \Solunes\Master\App\Node::create(['name'=>'customer', 'location'=>'customer', 'folder'=>'todotix']);
        if(config('customers.dependants')){
            $node_customer_dependant = \Solunes\Master\App\Node::create(['name'=>'customer-dependant', 'location'=>'customer', 'folder'=>'todotix']);
        }
        
        if($node_customer = \Solunes\Master\App\Node::where('name', 'customer')->first()){
            \Solunes\Master\App\NodeExtra::create(['parent_id'=>$node_customer->id, 'type'=>'action_field', 'parameter'=>'field', 'value_array'=>json_encode(["login-as","edit"])]);
        }
        if($node_payment = \Solunes\Master\App\Node::where('name', 'payment')->first()){
            \Solunes\Master\App\NodeExtra::create(['parent_id'=>$node_payment->id, 'type'=>'action_field', 'parameter'=>'field', 'value_array'=>json_encode(["manual-pay","edit"])]);
        }

        // Usuarios
        $admin = \Solunes\Master\App\Role::where('name', 'admin')->first();
        $member = \Solunes\Master\App\Role::where('name', 'member')->first();
        $dashboard_perm = \Solunes\Master\App\Permission::where('name','dashboard')->first();
        if(!\Solunes\Master\App\Permission::where('name','todotix')->first()){
            $customer_perm = \Solunes\Master\App\Permission::create(['name'=>'todotix', 'display_name'=>'Todotix']);
            $members_perm = \Solunes\Master\App\Permission::create(['name'=>'members', 'display_name'=>'Miembros']);
            $admin->permission_role()->attach([$customer_perm->id]);
            $member->permission_role()->attach([$dashboard_perm->id, $members_perm->id]);
        }

    }
}