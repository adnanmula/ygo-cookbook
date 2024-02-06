<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class YgoInitial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
        'CREATE TABLE ygo_cards (
                id uuid NOT NULL,
                refs jsonb NOT NULL default \'{}\',
                name jsonb NOT NULL default \'{}\',
                description jsonb NOT NULL default \'{}\',
                supertype character varying(50) NOT NULL,
                type character varying(50) NOT NULL,
                frame_type character varying(50) NOT NULL,
                wiki_url character varying(255) NOT NULL,
                atk integer NULL,
                def integer NULL,
                level integer NULL,
                category character varying(50) NULL,
                attribute character varying(50) NULL,
                pend_scale integer NULL,
                link_value integer NULL,
                formats jsonb NOT NULL default \'[]\',
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "ygo_cards"');
    }
}
