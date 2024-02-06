<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardAttribute: string implements \JsonSerializable
{
    use EnumHelper;

    case DARK = 'DARK';
    case EARTH = 'EARTH';
    case FIRE = 'FIRE';
    case LIGHT = 'LIGHT';
    case WATER = 'WATER';
    case WIND = 'WIND';
    case DIVINE = 'DIVINE';
}
