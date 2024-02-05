<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class YgoInitial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
        'CREATE TABLE ygo_cards (
                id uuid NOT NULL,
                name character varying(64) NOT NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "ygo_cards"');
    }
}
