<?php

namespace mirkhamidov\mail;


use Exception;

trait ModuleTrait
{
    /**
     * @var null|Module
     */
    private $_module = null;

    /**
     * @return null|Module
     * @throws \Exception
     */
    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('mmail');
        }

        if (!$this->_module) {
            throw new Exception(\Yii::t('app', 'Module not found. Add "mmail" module to config file.'));
        }

        return $this->_module;
    }
}