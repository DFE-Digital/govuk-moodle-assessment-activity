<?php

declare(strict_types=1);

namespace mod_assessment\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251021161328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE mdl_assessment_record (id SERIAL NOT NULL, assessment_id INT NOT NULL, user_id BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A407C5BCDD3DD5F1 ON mdl_assessment_record (assessment_id)');
        $this->addSql('CREATE TABLE mdl_assessment_record_item (id SERIAL NOT NULL, assessment_type_item_id INT NOT NULL, assessment_record_id INT NOT NULL, value JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_19BEC14C8225E769 ON mdl_assessment_record_item (assessment_type_item_id)');
        $this->addSql('CREATE INDEX IDX_19BEC14C9F8C86E0 ON mdl_assessment_record_item (assessment_record_id)');
        $this->addSql('CREATE TABLE mdl_assessment_type (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, guidance_before TEXT DEFAULT NULL, guidance_after TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mdl_assessment_type_item (id SERIAL NOT NULL, assessment_type_section_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(1000) DEFAULT NULL, guidance_before TEXT DEFAULT NULL, guidance_after TEXT DEFAULT NULL, display_order INT DEFAULT NULL, type VARCHAR(255) NOT NULL, data_type VARCHAR(255) DEFAULT NULL, is_required BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_705E152F6BCE28C ON mdl_assessment_type_item (assessment_type_section_id)');
        $this->addSql('CREATE TABLE mdl_assessment_type_section (id SERIAL NOT NULL, assessment_type_id INT NOT NULL, parent_assessment_type_section_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, guidance_before TEXT DEFAULT NULL, guidance_after TEXT DEFAULT NULL, display_order INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_16A217A6FB21D5D ON mdl_assessment_type_section (assessment_type_id)');
        $this->addSql('CREATE INDEX IDX_16A217A738FAE9 ON mdl_assessment_type_section (parent_assessment_type_section_id)');
        $this->addSql('ALTER TABLE mdl_assessment_record ADD CONSTRAINT FK_A407C5BCDD3DD5F1 FOREIGN KEY (assessment_id) REFERENCES mdl_assessment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mdl_assessment_record_item ADD CONSTRAINT FK_19BEC14C8225E769 FOREIGN KEY (assessment_type_item_id) REFERENCES mdl_assessment_type_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mdl_assessment_record_item ADD CONSTRAINT FK_19BEC14C9F8C86E0 FOREIGN KEY (assessment_record_id) REFERENCES mdl_assessment_record (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mdl_assessment_type_item ADD CONSTRAINT FK_705E152F6BCE28C FOREIGN KEY (assessment_type_section_id) REFERENCES mdl_assessment_type_section (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mdl_assessment_type_section ADD CONSTRAINT FK_16A217A6FB21D5D FOREIGN KEY (assessment_type_id) REFERENCES mdl_assessment_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mdl_assessment_type_section ADD CONSTRAINT FK_16A217A738FAE9 FOREIGN KEY (parent_assessment_type_section_id) REFERENCES mdl_assessment_type_section (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mdl_assessment ADD assessment_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD course BIGINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD timecreated BIGINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD timemodified BIGINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD intro TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ADD introformat SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE mdl_assessment ALTER id TYPE INT');
        $this->addSql('ALTER TABLE mdl_assessment ADD CONSTRAINT FK_F6106B106FB21D5D FOREIGN KEY (assessment_type_id) REFERENCES mdl_assessment_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F6106B106FB21D5D ON mdl_assessment (assessment_type_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mdl_assessment DROP CONSTRAINT FK_F6106B106FB21D5D');
        $this->addSql('ALTER TABLE mdl_assessment_record DROP CONSTRAINT FK_A407C5BCDD3DD5F1');
        $this->addSql('ALTER TABLE mdl_assessment_record_item DROP CONSTRAINT FK_19BEC14C8225E769');
        $this->addSql('ALTER TABLE mdl_assessment_record_item DROP CONSTRAINT FK_19BEC14C9F8C86E0');
        $this->addSql('ALTER TABLE mdl_assessment_type_item DROP CONSTRAINT FK_705E152F6BCE28C');
        $this->addSql('ALTER TABLE mdl_assessment_type_section DROP CONSTRAINT FK_16A217A6FB21D5D');
        $this->addSql('ALTER TABLE mdl_assessment_type_section DROP CONSTRAINT FK_16A217A738FAE9');
        $this->addSql('DROP TABLE mdl_assessment_record');
        $this->addSql('DROP TABLE mdl_assessment_record_item');
        $this->addSql('DROP TABLE mdl_assessment_type');
        $this->addSql('DROP TABLE mdl_assessment_type_item');
        $this->addSql('DROP TABLE mdl_assessment_type_section');
        $this->addSql('DROP INDEX IDX_F6106B106FB21D5D');
        $this->addSql('ALTER TABLE mdl_assessment DROP assessment_type_id');
        $this->addSql('ALTER TABLE mdl_assessment DROP course');
        $this->addSql('ALTER TABLE mdl_assessment DROP name');
        $this->addSql('ALTER TABLE mdl_assessment DROP created_at');
        $this->addSql('ALTER TABLE mdl_assessment DROP updated_at');
        $this->addSql('ALTER TABLE mdl_assessment DROP timecreated');
        $this->addSql('ALTER TABLE mdl_assessment DROP timemodified');
        $this->addSql('ALTER TABLE mdl_assessment DROP intro');
        $this->addSql('ALTER TABLE mdl_assessment DROP introformat');
        $this->addSql('ALTER TABLE mdl_assessment ALTER id TYPE BIGINT');
    }
}
