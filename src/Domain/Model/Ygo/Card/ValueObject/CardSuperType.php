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

    public static function fromFrameType(CardFrameType $frameType): self
    {
        if ($frameType->isMonster()) {
            return self::MONSTER;
        }

        if ($frameType->isSpell()) {
            return self::SPELL;
        }

        if ($frameType->isTrap()) {
            return self::TRAP;
        }

        if ($frameType->isSkill()) {
            return self::SKILL;
        }

        throw new \Exception('Invalid frameType');
    }
}