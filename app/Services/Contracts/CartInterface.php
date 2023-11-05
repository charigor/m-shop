<?php

namespace App\Services\Contracts;

interface CartInterface
{
    public function add($item);

    public function get();

    public function getTotal();

    public function clear();

    public function getCookieCart();
}
