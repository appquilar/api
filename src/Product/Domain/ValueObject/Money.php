<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use InvalidArgumentException;

#[Embeddable]
class Money
{
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $amount;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    public function __construct(float $amount, string $currency = 'EUR')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }

        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency code must be 3 characters long');
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot add money with different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot subtract money with different currencies');
        }

        $result = $this->amount - $other->amount;
        if ($result < 0) {
            throw new InvalidArgumentException('Cannot result in negative money');
        }

        return new self($result, $this->currency);
    }

    public function multiply(float $factor): self
    {
        if ($factor < 0) {
            throw new InvalidArgumentException('Cannot multiply by negative factor');
        }

        return new self($this->amount * $factor, $this->currency);
    }

    public function __toString(): string
    {
        return $this->amount . ' ' . $this->currency;
    }
}