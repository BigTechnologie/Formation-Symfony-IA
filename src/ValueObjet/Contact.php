<?php

namespace App\ValueObjet;

class Contact
{

    public function __construct(
        private readonly ?string $firstname,
        private readonly ?string $lastname,
        private readonly bool $company = false,
    ) {

    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }


    public function getLastname()
    {
        return $this->lastname;
    }

    public function isCompany(): bool 
    {
        return $this->company;
    }
}