<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mail_log`.
 * Has foreign keys to the tables:
 *
 * - `mail`
 */
class m161113_065542_create_mail_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('mail_log', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'mail_id' => $this->integer(),
            'status' => $this->smallInteger()->defaultValue(0)->comment('0-default'),
            'recipient' => $this->string()->defaultValue(null)->comment('Mail Recipient'),
            'sender' => $this->string()->defaultValue(null)->comment('Mail Sender'),
            'composed_text' => $this->text()->defaultValue(null)->comment('Composed mail by text'),
            'composed_html' => $this->text()->defaultValue(null)->comment('Composed mail by Html'),
            'data_data' => $this->text()->defaultValue(null)->comment('JSON data'),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
            "KEY `status` (`status`)",
            "KEY `recipient` (`recipient`)",
        ]);

        // creates index for column `mail_id`
        $this->createIndex(
            'idx-mail_log-mail_id',
            'mail_log',
            'mail_id'
        );

        // add foreign key for table `mail`
        $this->addForeignKey(
            'fk-mail_log-mail_id',
            'mail_log',
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
            'fk-mail_log-mail_id',
            'mail_log'
        );

        // drops index for column `mail_id`
        $this->dropIndex(
            'idx-mail_log-mail_id',
            'mail_log'
        );

        $this->dropTable('mail_log');
    }
}
