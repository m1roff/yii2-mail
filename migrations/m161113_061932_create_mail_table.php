<?php

use yii\db\Migration;

/**
 * Handles the creation of table `mail`.
 */
class m161113_061932_create_mail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('mail', [
            'id' => $this->primaryKey(),
            'alias' => $this->string()->unique()->comment('Mail Template Alias'),
            'is_active' => $this->boolean()->defaultValue(true)->comment('Is this item active?'),
            'name' => $this->string()->notNull()->comment('Arbitrary name'),
            'subject' => $this->string()->notNull()->comment('E-mail subject'),
            'content_text' => $this->string()->notNull()->comment('Alias for text message representation'),
            'content_html' => $this->string()->notNull()->comment('Alias for html message representation'),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('mail');
    }
}
