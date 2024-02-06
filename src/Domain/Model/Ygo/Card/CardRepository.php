<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card;

interface CardRepository
{
    public function save(Card $card): void;
}