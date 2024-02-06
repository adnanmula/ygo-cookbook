<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum Format: string implements \JsonSerializable
{
    use EnumHelper;

    case TCG = 'TCG';
    case OCG = 'OCG';
    case GOAT = 'GOAT';
    case OCG_GOAT = 'OCG GOAT';
    case DUEL_LINKS = 'Duel Links';
    case RUSH_DUEL = 'Rush Duel';
    case SPEED_DUEL = 'Speed Duel';
    case COMMON_CHARITY = 'Common Charity';
    case EDISON = 'Edison';
}
