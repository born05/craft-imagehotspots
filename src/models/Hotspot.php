<?php
namespace born05\imagehotspots\models;

use Craft;
use craft\base\Model;

class Hotspot extends Model
{
    /**
     * @var float Defaults to the center.
     */
    public $x = 0.5;

    /**
     * @var float Defaults to the center.
     */
    public $y = 0.5;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['x', 'y'], 'required'],
            [['x', 'y'], 'double', 'min' => 0, 'max' => 1],
        ];
    }

    /**
     * Returns the coords in X;Y syntax.
     *
     * @return string
     */
    public function getCoords(): string
    {
        return "{$this->x};{$this->y})";
    }

    /**
     * Returns the coords as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
