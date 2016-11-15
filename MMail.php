<?php
/**
 * Shortcut for Mail module
 */


namespace mirkhamidov\mail;


use yii\base\Exception;

class MMail
{
    /**
     * @var null|\mirkhamidov\mail\Module
     */
    private static $_module = null;

    /**
     * @return \mirkhamidov\mail\Module
     */
    public static function module()
    {
        self::init();
        return self::$_module;
    }

    /**
     * @throws Exception
     */
    public static function init()
    {
        if (self::$_module === null) {
            self::$_module = \Yii::$app->getModule('mmail');
        }

        if (!self::$_module) {
            throw new Exception(\Yii::t('app', 'Module not found. Add "mmail" module to config file.'));
        }
    }

    public static function linkButton($mailId, $recipient, array $moreData = [], array $params = [])
    {
        return self::module()->linkButton($mailId, $recipient, $moreData, $params);
    }

    public static function sendMail($mailId, $recipient, array $moreData = [], array $params = [])
    {
        return self::module()->sendMail($mailId, $recipient, $moreData, $params);
    }

    public static function getSendMailErrors()
    {
        return self::module()->getSendMailErrors();
    }


}