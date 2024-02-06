<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardType: string implements \JsonSerializable
{
    use EnumHelper;

    //Main deck
    case EFFECT_MONSTER = 'Effect Monster';
    case FLIP_EFFECT_MONSTER = 'Flip Effect Monster';
    case FLIP_TUNER_EFFECT_MONSTER = 'Flip Tuner Effect Monster';
    case GEMINI_MONSTER = 'Gemini Monster';
    case NORMAL_MONSTER = 'Normal Monster';
    case NORMAL_TUNER_MONSTER = 'Normal Tuner Monster';
    case PENDULUM_EFFECT_MONSTER = 'Pendulum Effect Monster';
    case PENDULUM_EFFECT_RITUAL_MONSTER = 'Pendulum Effect Ritual Monster';
    case PENDULUM_FLIP_EFFECT_MONSTER = 'Pendulum Flip Effect Monster';
    case PENDULUM_NORMAL_MONSTER = 'Pendulum Normal Monster';
    case PENDULUM_TUNER_EFFECT_MONSTER = 'Pendulum Tuner Effect Monster';
    case RITUAL_EFFECT_MONSTER = 'Ritual Effect Monster';
    case RITUAL_MONSTER = 'Ritual Monster';
    case SPELL_CARD = 'Spell Card';
    case SPIRIT_MONSTER = 'Spirit Monster';
    case TOON_MONSTER = 'Toon Monster';
    case TRAP_CARD = 'Trap Card';
    case TUNER_MONSTER = 'Tuner Monster';
    case UNION_EFFECT_MONSTER = 'Union Effect Monster';

    //Extra deck
    case FUSION_MONSTER = 'Fusion Monster';
    case LINK_MONSTER = 'Link Monster';
    case PENDULUM_EFFECT_FUSION_MONSTER = 'Pendulum Effect Fusion Monster';
    case SYNCHRO_MONSTER = 'Synchro Monster';
    case SYNCHRO_PENDULUM_EFFECT_MONSTER = 'Synchro Pendulum Effect Monster';
    case SYNCHRO_TUNER_MONSTER = 'Synchro Tuner Monster';
    case XYZ_MONSTER = 'XYZ Monster';
    case XYZ_PENDULUM_EFFECT_MONSTER = 'XYZ Pendulum Effect Monster';

    //Other
    case SKILL_CARD = 'Skill Card';
    case TOKEN = 'Token';
}