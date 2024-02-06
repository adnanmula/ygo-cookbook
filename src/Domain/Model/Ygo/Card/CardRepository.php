<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card;

use AdnanMula\Criteria\Criteria;

interface CardRepository
{
    public function save(Card $card): void;

    /** @return array<Card> */
    public function search(Criteria $criteria): array;
}