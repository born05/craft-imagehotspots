<?php
namespace born05\imagehotspots\gql;

use craft\gql\GqlEntityRegistry;
use craft\gql\base\GqlTypeTrait;
use GraphQL\Type\Definition\Type;

class HotspotType
{
    use GqlTypeTrait;

    public static function getName(): string
    {
        return 'Hotspot';
    }

    public static function getFieldDefinitions(): array
    {
        return [
            'x'     => [
                'name'        => 'x',
                'type'        => Type::float(),
                'description' => 'The x coordinate.',
            ],
            'y'     => [
                'name'        => 'y',
                'type'        => Type::float(),
                'description' => 'The y coordinate.',
            ],
        ];
    }
}
