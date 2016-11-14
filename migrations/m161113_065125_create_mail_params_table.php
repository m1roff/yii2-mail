<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mail_params`.
 * Has foreign keys to the tables:
 *
 * - `mail`
 */
class m161113_065125_create_mail_params_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('mail_params', [
            'id' => $this->primaryKey(),
            'mail_id' => $this->integer(),
            'key' => $this->string()->notNull()->comment('Param Key'),
            'value' => $this->text()->defaultValue(null)->comment('Param Value'),
            'value_default' => $this->text()->defaultValue(null)->comment('Param Default Value'),
            'rules_data' => $this->text()->defaultValue(null)->comment('Param Rules for Yii2-rules'),
            "UNIQUE KEY `uniq_mail_key` (`mail_id`, `key`)",
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        // creates index for column `mail_id`
        $this->createIndex(
            'idx-mail_params-mail_id',
            'mail_params',
            'mail_id'
        );

        // add foreign key for table `mail`
        $this->addForeignKey(
            'fk-mail_params-mail_id',
            'mail_params',
            'mail_id',
            'mail',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `mail`
        $this->dropForeignKey(
            'fk-mail_params-mail_id',
            'mail_params'
        );

        // drops index for column `mail_id`
        $this->dropIndex(
            'idx-mail_params-mail_id',
            'mail_params'
        );

        $this->dropTable('mail_params');
    }
}
