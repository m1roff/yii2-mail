<?php

namespace mirkhamidov\mail\assets;


use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class LinkButtonAssets
 * @package mirkhamidov\mail\assets
 */
class LinkButtonAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'link_button';

    public $depends = [
        JqueryAsset::class,
    ];

    public $js = [];

    public $css = [
        'css/css.css',
    ];
}