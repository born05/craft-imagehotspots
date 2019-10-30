<?php

namespace born05\imagehotspots;

use born05\imagehotspots\fields\ImageHotspots as ImageHotspotsField;

use Craft;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    public static $plugin;

    public $schemaVersion = '1.0.0';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ImageHotspotsField::class;
            }
        );
    }
}
