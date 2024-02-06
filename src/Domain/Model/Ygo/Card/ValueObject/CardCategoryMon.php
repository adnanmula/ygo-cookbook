<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject;

use AdnanMula\Cards\Shared\EnumHelper;

enum CardCategoryMon: string implements \JsonSerializable
{
    use EnumHelper;

    case AQUA = 'Aqua';
    case BEAST = 'Beast';
    case BEAST_WARRIOR = 'Beast-Warrior';
    case CREATOR_GOD = 'Creator-God';
    case CYBERSE = 'Cyberse';
    case DINOSAUR = 'Dinosaur';
    case DIVINE_BEST = 'Divine-Beast';
    case DRAGON = 'Dragon';
    case FAIRY = 'Fairy';
    case FIEND = 'Fiend';
    case FISH = 'Fish';
    case INSECT = 'Insect';
    case MACHINE = 'Machine';
    case PLANT = 'Plant';
    case PSYCHIC = 'Psychic';
    case PYRO = 'Pyro';
    case REPTILE = 'Reptile';
    case ROCK = 'Rock';
    case SEA_SERPENT = 'Sea Serpent';
    case SPELLCASTER = 'Spellcaster';
    case THUNDER = 'Thunder';
    case WARRIOR = 'Warrior';
    case WINGED_BEST = 'Winged Beast';
    case WYRM = 'Wyrm';
    case ZOMBIE = 'Zombie';
}
