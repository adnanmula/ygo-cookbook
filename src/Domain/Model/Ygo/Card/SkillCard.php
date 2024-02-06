<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Ygo\Card;

use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardCategorySkill;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardFrameType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardSuperType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardType;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\WikiUrl;

final readonly class SkillCard extends Card
{
    public function __construct(
        int $id,
        string $name,
        CardType $type,
        CardFrameType $frameType,
        string $description,
        WikiUrl $wikiUrl,
        public CardCategorySkill $category,
    ) {
        parent::__construct($id, $name, $type, $frameType, $description, $wikiUrl);
    }

    public function supertype(): CardSuperType
    {
        return CardSuperType::SKILL;
    }
}
