<?php


namespace App\Services;


class PrivateService implements Payment
{
        public function pay(){
            return 'private';
        }
}
