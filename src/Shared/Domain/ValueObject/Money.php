<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Exception\InvalidMoneyException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Money
{
    private const string EUR = 'EUR';
    private const array ALLOWED_CURRENCIES = [
        self::EUR
    ];

    #[ORM\Column(type: 'integer')]
    private int $amount;
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    public function __construct(int $amount, string $currency = 'EUR')
    {
        if ($amount < 0) {
            throw new InvalidMoneyException('money.amount.negative');
        }

        if (
            strlen($currency) !== 3 ||
            !in_array($currency, self::ALLOWED_CURRENCIES, true)
        ) {
            throw new InvalidMoneyException('money.currency.ISO_4217');
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['amount'],
            $data['currency']
        );
    }
}