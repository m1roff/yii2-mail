<?php

use yii\db\Migration;

/**
 * Handles adding config_data to table `mail`.
 */
class m161115_105322_add_config_data_column_to_mail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('mail', 'config_data', $this->text()->defaultValue(null)->comment('in Json'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('mail', 'config_data');
    }
}
