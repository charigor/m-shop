<?php


namespace App\Services\Test;


class TestService
{
    public $t;
    public function __construct($num)
    {
            $this->t = $num;
    }
    /**
     * @return int
     */
        public function start()
        {
            return $this->t;
        }

    /**
     * @return int
     */
        public function minus()
        {
           return  $this->start() - 80 ;
        }

    /**
     * @return int
     */
    public function plus()
    {
        return $this->start() + 80 ;
    }
}
