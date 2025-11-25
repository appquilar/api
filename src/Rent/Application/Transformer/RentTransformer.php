<?php declare(strict_types=1);

namespace App\Rent\Application\Transformer;

use App\Rent\Domain\Entity\Rent;

class RentTransformer
{
    public function transform(Rent $rent): array
    {
        return [
            'rent_id'           => $rent->getId()->toString(),
            'product_id'        => $rent->getProductId()->toString(),
            'owner_id'          => $rent->getOwnerId()->toString(),
            'owner_type'        => $rent->getOwnerType()->value,
            'renter_id'         => $rent->getRenterId()->toString(),
            'start_date'        => $rent->getStartDate()->format('Y-m-d H:i:s e'),
            'end_date'          => $rent->getEndDate()->format('Y-m-d H:i:s e'),
            'deposit' => [
                'amount'   => $rent->getDeposit()->getAmount(),
                'currency' => $rent->getDeposit()->getCurrency(),
            ],
            'price' => [
                'amount'   => $rent->getPrice()->getAmount(),
                'currency' => $rent->getPrice()->getCurrency(),
            ],
            'deposit_returned' => $rent->getDepositReturned() ? [
                'amount'   => $rent->getDepositReturned()->getAmount(),
                'currency' => $rent->getDepositReturned()->getCurrency(),
            ] : null,
            'status' => $rent->getStatus()->value,
        ];
    }
}
