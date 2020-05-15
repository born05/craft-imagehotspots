<?php

namespace born05\imagehotspots;

use born05\imagehotspots\fields\ImageHotspots as ImageHotspotsField;
use born05\imagehotspots\gql\HotspotType;

use Craft;
use craft\services\Fields;
use craft\services\Gql;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlTypesEvent;

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

        Event::on(
            Gql::class, 
            Gql::EVENT_REGISTER_GQL_TYPES, 
            function (RegisterGqlTypesEvent $event) {
                $event->types[] = HotspotType::class;
            }
        );
    }
}
