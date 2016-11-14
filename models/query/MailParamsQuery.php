<?php

namespace mirkhamidov\mail\models\query;

/**
 * This is the ActiveQuery class for [[\mirkhamidov\mail\models\base\MailParams]].
 *
 * @see \mirkhamidov\mail\models\base\MailParams
 */
class MailParamsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \mirkhamidov\mail\models\base\MailParams[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \mirkhamidov\mail\models\base\MailParams|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
