<?php

namespace Todotix\Customer\App\Listeners;

class CustomerSaving {

    public function handle($event) {
        $full_name = $event->first_name.' '.$event->last_name;
        $user = \App\User::where('email',$event->email)->orWhere('cellphone',$event->phone)->orWhere('username',$event->ci_number)->first();
        if(!$user){
            if(!$event->password){
                $password = rand(100000,999999);
            } else {
                $password = NULL;
            }
            $user = new \App\User;
            $user->name = $full_name;
            $user->email = $event->email;
            $user->cellphone = $event->phone;
            $user->username = $event->ci_number;
            $user->password = $password;
            $user->save();
            $user->role_user()->attach(2); // Agregar como miembro
        } else {
            if($user->name!=$full_name){
                $user->name = $full_name;
            }
            if(!$user->email&&!\App\User::where('email', $event->email)->first()){
                $user->email = $event->email;
            }
            if(!$user->cellphone&&!\App\User::where('cellphone', $event->phone)->first()){
                $user->cellphone = $event->phone;
            }
            if(!$user->username&&!\App\User::where('username', $event->ci_number)->first()){
                $user->username = $event->ci_number;
            }
            $user->save();
        }
        if(!$event->user_id){
            $event->user_id = $user->id;
        }
        if($event->password){
            $event->password = NULL;
        }
        $event->name = $full_name;
        return $event;
    }

}