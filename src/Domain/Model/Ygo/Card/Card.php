<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card;

use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardAttribute;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardCategoryMon;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardCategorySkill;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardCategorySpell;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardCategoryTrap;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardFrameType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardSuperType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\Format;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\WikiUrl;

abstract readonly class Card
{
    public array $formats;

    public function __construct(
        public int $id,
        public string $name,
        public CardType $type,
        public CardFrameType $frameType,
        public string $description,
        public WikiUrl $wikiUrl,
        Format ...$formats,
    ) {
        $this->formats = $formats;
    }

    abstract public function supertype(): CardSuperType;

    public static function fromYgoProArray(array $data)
    {
        $frameType = CardFrameType::from($data['frameType']);

        if ($frameType->isMonster()) {
            return new MonsterCard(
                $data['id'],
                $data['name'],
                CardType::from($data['type']),
                $frameType,
                $data['desc'],
                WikiUrl::from($data['ygoprodeck_url']),
                $data['atk'],
                $data['def'] ?? null,
                $data['level'],
                CardCategoryMon::from($data['race']),
                CardAttribute::from($data['attribute']),
            );
        }

        if ($frameType->isSpell()) {
            return new SpellCard(
                $data['id'],
                $data['name'],
                CardType::from($data['type']),
                $frameType,
                $data['desc'],
                WikiUrl::from($data['ygoprodeck_url']),
                CardCategorySpell::from($data['race']),
            );
        }

        if ($frameType->isTrap()) {
            return new TrapCard(
                $data['id'],
                $data['name'],
                CardType::from($data['type']),
                $frameType,
                $data['desc'],
                WikiUrl::from($data['ygoprodeck_url']),
                CardCategoryTrap::from($data['race']),
            );
        }

        if ($frameType->isSkill()) {
            return new SkillCard(
                $data['id'],
                $data['name'],
                CardType::from($data['type']),
                $frameType,
                $data['desc'],
                WikiUrl::from($data['ygoprodeck_url']),
                CardCategorySkill::from($data['race']),
            );
        }

        throw new \Exception('Invalid card type');
    }
}
