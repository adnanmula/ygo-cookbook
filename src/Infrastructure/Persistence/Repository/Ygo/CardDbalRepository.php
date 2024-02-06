<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo;

use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;

final class CardDbalRepository extends DbalRepository implements CardRepository
{
    public function save(Card $card): void
    {

    }
}