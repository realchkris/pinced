<?php

namespace App\DTO;

class RestaurantDTO
{
    public string $name;
    public string $address;
    public ?string $sourceUrl = null;

    public function __construct(string $name, string $address, ?string $sourceUrl = null)
    {
        $this->name = trim($name);
        $this->address = trim($address);
        $this->sourceUrl = $sourceUrl;
    }
}
