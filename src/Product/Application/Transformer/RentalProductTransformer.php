<?php

declare(strict_types=1);

namespace App\Product\Application\Transformer;

use App\Product\Domain\Entity\RentalProduct;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;

class RentalProductTransformer implements Transformer
{
    public function transform(RentalProduct|Entity $entity): array
    {
        return [
            'id' => $entity->getId()->toString(),
            'daily_price' => [
                'amount' => $entity->getDailyPrice()->getAmount(),
                'currency' => $entity->getDailyPrice()->getCurrency(),
            ],
            'hourly_price' => $entity->getHourlyPrice() ? [
                'amount' => $entity->getHourlyPrice()->getAmount(),
                'currency' => $entity->getHourlyPrice()->getCurrency(),
            ] : null,
            'weekly_price' => $entity->getWeeklyPrice() ? [
                'amount' => $entity->getWeeklyPrice()->getAmount(),
                'currency' => $entity->getWeeklyPrice()->getCurrency(),
            ] : null,
            'monthly_price' => $entity->getMonthlyPrice() ? [
                'amount' => $entity->getMonthlyPrice()->getAmount(),
                'currency' => $entity->getMonthlyPrice()->getCurrency(),
            ] : null,
            'deposit' => $entity->getDeposit() ? [
                'amount' => $entity->getDeposit()->getAmount(),
                'currency' => $entity->getDeposit()->getCurrency(),
            ] : null
        ];
    }
}