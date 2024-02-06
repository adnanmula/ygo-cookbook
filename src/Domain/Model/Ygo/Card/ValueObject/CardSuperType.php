<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardSuperType: string implements \JsonSerializable
{
    use EnumHelper;

    case MONSTER = 'MONSTER';
    case SPELL = 'SPELL';
    case TRAP = 'TRAP';
    case SKILL = 'SKILL';
}