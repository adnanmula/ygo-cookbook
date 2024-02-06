<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum Format: string implements \JsonSerializable
{
    use EnumHelper;

    case TCG = 'tcg';
    case OCG = 'ocg';
    case GOAT = 'goat';
    case OCG_GOAT = 'ocg goat';
    case DUEL_LINKS = 'duel links';
    case RUSH_DUEL = 'rush duel';
    case SPEED_DUEL = 'speed duel';
}