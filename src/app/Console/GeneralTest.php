<?php

namespace Todotix\Customer\App\Console;

use Illuminate\Console\Command;

class GeneralTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'general-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza una creacion de usuario y edicion.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $to_array = ['edumejia30@gmail.com','edu_mejia30@hotmail.com'];
        $this->info('Comenzando la prueba de Email enviando a MK: '.$to_array);
        /*$response = \Customer::sendEmail($email_title, $to_array, $message_title, $message_content);
        $this->info('Email enviado a MK correctamente. Respuesta: '.$response);*/
    }
}
