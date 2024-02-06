<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Persistence\Repository\Ygo;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Ygo\Card\Card;
use AdnanMula\Cards\Domain\Model\Ygo\Card\CardRepository;
use AdnanMula\Cards\Domain\Model\Ygo\Card\MonsterCard;
use AdnanMula\Cards\Domain\Model\Ygo\Card\SkillCard;
use AdnanMula\Cards\Domain\Model\Ygo\Card\SpellCard;
use AdnanMula\Cards\Domain\Model\Ygo\Card\TrapCard;
use AdnanMula\Cards\Domain\Model\Ygo\Card\ValueObject\CardSuperType;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\DbalRepository;
use Doctrine\DBAL\Statement;

final class CardDbalRepository extends DbalRepository implements CardRepository
{
    private const TABLE = 'ygo_cards';

    public function save(Card $card): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, supertype, type, frame_type, "desc", wiki_url, atk, def, level, category, attribute, pend_scale, link_value, formats)
                    VALUES (:id, :name, :supertype, :type, :frame_type, :desc, :wiki_url, :atk, :def, :level, :category, :attribute, :pend_scale, :link_value, :formats)
                    ON CONFLICT (id) DO NOTHING
                ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $card->id);
        $stmt->bindValue(':name', $card->name);
        $stmt->bindValue(':supertype', $card->supertype()->value);
        $stmt->bindValue(':type', $card->type->value);
        $stmt->bindValue(':frame_type', $card->frameType->value);
        $stmt->bindValue(':desc', $card->description);
        $stmt->bindValue(':wiki_url', $card->wikiUrl->value());
        $stmt->bindValue(':formats', Json::encode($card->formats));

        if ($card->supertype() === CardSuperType::MONSTER) {
            $stmt = $this->saveMonster($card, $stmt);
        }

        if ($card->supertype() === CardSuperType::SPELL) {
            $stmt = $this->saveSpell($card, $stmt);
        }

        if ($card->supertype() === CardSuperType::TRAP) {
            $stmt = $this->saveTrap($card, $stmt);
        }

        if ($card->supertype() === CardSuperType::SKILL) {
            $stmt = $this->saveSkill($card, $stmt);
        }

        $stmt->executeStatement();
    }

    private function saveMonster(MonsterCard $card, Statement $stmt): Statement
    {
        $stmt->bindValue(':atk', $card->atk);
        $stmt->bindValue(':def', $card->def);
        $stmt->bindValue(':level', $card->level);
        $stmt->bindValue(':category', $card->category->value);
        $stmt->bindValue(':attribute', $card->attribute->value);
        $stmt->bindValue(':pend_scale', null);
        $stmt->bindValue(':link_value', null);

        return $stmt;
    }

    private function saveSpell(SpellCard $card, Statement $stmt): Statement
    {
        $stmt->bindValue(':atk', null);
        $stmt->bindValue(':def', null);
        $stmt->bindValue(':level', null);
        $stmt->bindValue(':category', $card->category->value);
        $stmt->bindValue(':attribute', null);
        $stmt->bindValue(':pend_scale', null);
        $stmt->bindValue(':link_value', null);

        return $stmt;
    }

    private function saveTrap(TrapCard $card, Statement $stmt): Statement
    {
        $stmt->bindValue(':atk', null);
        $stmt->bindValue(':def', null);
        $stmt->bindValue(':level', null);
        $stmt->bindValue(':category', $card->category->value);
        $stmt->bindValue(':attribute', null);
        $stmt->bindValue(':pend_scale', null);
        $stmt->bindValue(':link_value', null);

        return $stmt;
    }

    private function saveSkill(SkillCard $card, Statement $stmt): Statement
    {
        $stmt->bindValue(':atk', null);
        $stmt->bindValue(':def', null);
        $stmt->bindValue(':level', null);
        $stmt->bindValue(':category', $card->category->value());
        $stmt->bindValue(':attribute', null);
        $stmt->bindValue(':pend_scale', null);
        $stmt->bindValue(':link_value', null);

        return $stmt;
    }
}