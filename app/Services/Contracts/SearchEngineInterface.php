<?php

namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface SearchEngineInterface
{
    public function handle(Request $request): array;

    public function search($index, array $body);
}
