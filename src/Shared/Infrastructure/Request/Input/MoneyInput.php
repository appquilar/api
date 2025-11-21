<?php declare(strict_types=1);

namespace App\Shared\Infrastructure\Request\Input;

use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Validator\Constraints as Assert;

class MoneyInput
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\GreaterThanOrEqual(0)]
        public int $amount = 0,

        #[Assert\NotBlank]
        #[Assert\Length(exactly: 3)]
        public string $currency = 'EUR',
    ) {}

    public function toMoney(): Money
    {
        return new Money($this->amount, $this->currency);
    }
}