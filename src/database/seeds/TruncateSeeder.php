<?php

namespace Todotix\Customer\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class TruncateSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(config('customers.dependants')){
        	\Todotix\Customer\App\CustomerDependant::truncate();
        }
        \Todotix\Customer\App\Customer::truncate();
    }
}