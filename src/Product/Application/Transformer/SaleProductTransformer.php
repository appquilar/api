<?php

declare(strict_types=1);

namespace App\Product\Application\Transformer;

use App\Product\Domain\Entity\SaleProduct;
use App\Shared\Application\Transformer\Transformer;
use App\Shared\Domain\Entity\Entity;

class SaleProductTransformer implements Transformer
{
    public function transform(SaleProduct|Entity $entity): array
    {
        return [
            'id' => $entity->getId()->toString(),
            'price' => [
                'amount' => $entity->getPrice()->getAmount(),
                'currency' => $entity->getPrice()->getCurrency(),
            ],
            'condition' => $entity->getCondition(),
            'year_of_purchase' => $entity->getYearOfPurchase(),
            'negotiable' => $entity->isNegotiable(),
            'additional_information' => $entity->getAdditionalInformation(),
        ];
    }
}