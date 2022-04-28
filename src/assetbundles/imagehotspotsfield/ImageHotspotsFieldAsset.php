<?php

namespace born05\imagehotspots\assetbundles\imagehotspotsfield;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @inheritdoc
 */
class ImageHotspotsFieldAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@born05/imagehotspots/assetbundles/imagehotspotsfield/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/Editor.js',
            'js/Input.js',
        ];

        $this->css = [
            'css/Input.css',
        ];

        parent::init();
    }
}
