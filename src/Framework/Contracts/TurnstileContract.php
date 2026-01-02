<?php
namespace Framework\Contracts;

interface TurnstileContract
{
    public function post(string $url, array $form): array;
}

