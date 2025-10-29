<?php

declare(strict_types=1);

namespace mod_assessment\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // An empty up: this migration exists solely to bootstrap Doctrine migrations
    }

    public function down(Schema $schema): void
    {
        // An empty down: this migration exists solely to bootstrap Doctrine migrations
    }
}
