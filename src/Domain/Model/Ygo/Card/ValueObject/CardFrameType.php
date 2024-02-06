<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardFrameType: string implements \JsonSerializable
{
    use EnumHelper;

    case NORMAL = 'normal';
    case EFFECT = 'effect';
    case RITUAL = 'ritual';
    case FUSION = 'fusion';
    case SYNCHRO = 'synchro';
    case XYZ = 'xyz';
    case LINK = 'link';
    case NORMAL_PENDULUM = 'normal_pendulum';
    case EFFECT_PENDULUM = 'effect_pendulum';
    case RITUAL_PENDULUM = 'ritual_pendulum';
    case FUSION_PENDULUM = 'fusion_pendulum';
    case SYNCHRO_PENDULUM = 'synchro_pendulum';
    case XYZ_PENDULUM = 'xyz_pendulum';
    case SPELL = 'spell';
    case TRAP = 'trap';
    case SKILL = 'skill';
    case TOKEN = 'token';

    public function isMonster(): bool
    {
        return $this === self::NORMAL
            || $this === self::EFFECT
            || $this === self::RITUAL
            || $this === self::FUSION
            || $this === self::SYNCHRO
            || $this === self::XYZ
            || $this === self::LINK
            || $this === self::NORMAL_PENDULUM
            || $this === self::EFFECT_PENDULUM
            || $this === self::RITUAL_PENDULUM
            || $this === self::FUSION_PENDULUM
            || $this === self::SYNCHRO_PENDULUM
            || $this === self::XYZ_PENDULUM;
    }

    public function isLinkMonster(): bool
    {
        return $this === self::LINK;
    }

    public function isXyzMonster(): bool
    {
        return $this === self::XYZ
            || $this === self::XYZ_PENDULUM;
    }

    public function isSpell(): bool
    {
        return $this === self::SPELL;
    }

    public function isTrap(): bool
    {
        return $this === self::TRAP;
    }

    public function isSkill(): bool
    {
        return $this === self::SKILL;
    }
}