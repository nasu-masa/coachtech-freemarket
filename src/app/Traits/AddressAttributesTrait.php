<?php

namespace App\Traits;

trait AddressAttributesTrait
{
    public function toAddressAttributes()
    {
        return $this->only('postal_code', 'address', 'building');
    }
}
