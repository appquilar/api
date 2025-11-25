<?php declare(strict_types=1);

namespace App\Tests\Factories\Rent\Domain;

class RentFactory extends PersistingRentFactory
{
    protected function initialize(): static
    {
        return $this
            ->withoutPersisting();
    }
}
