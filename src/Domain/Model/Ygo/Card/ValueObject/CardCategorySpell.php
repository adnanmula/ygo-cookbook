<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardCategorySpell: string implements \JsonSerializable
{
    use EnumHelper;

    case NORMAL = 'Normal';
    case FIELD = 'Field';
    case EQUIP = 'Equip';
    case CONTINUOUS = 'Continuous';
    case QUICK_PLAY = 'Quick-Play';
    case RITUAL = 'Ritual';
}
