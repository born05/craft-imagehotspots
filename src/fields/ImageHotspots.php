<?php
namespace born05\imagehotspots\fields;

use born05\imagehotspots\Plugin;
use born05\imagehotspots\models\Hotspot;
use born05\imagehotspots\gql\HotspotType;
use born05\imagehotspots\assetbundles\imagehotspotsfield\ImageHotspotsFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;
use yii\db\Schema;

class ImageHotspots extends Field
{
    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('imagehotspots', 'Image Hotspot');
    }

    /**
     * @inheritdoc
     */
    public static function valueType(): string
    {
        return Hotspot::class . '|null';
    }

    // Properties
    // =========================================================================

    /**
     * @var string|null The handle of the related asset field
     */
    public $relatedAssetHandle;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('imagehotspots/_components/field/settings', [
            'label' => Craft::t('imagehotspots', 'Related asset field handle'),
            'id' => 'relatedAssetHandle',
            'name' => 'relatedAssetHandle',
            'value' => $this->relatedAssetHandle,
            'errors' => $this->getErrors('relatedAssetHandle'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['relatedAssetHandle'], 'string', 'max' => 100];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($value instanceof Hotspot) {
            return $value;
        }

        if (is_string($value)) {
            $data = Json::decodeIfJson($value);
            return new Hotspot($data);
        }

        if (is_array($value)) {
            return new Hotspot([
                'x' => floatval($value['x']),
                'y' => floatval($value['y']),
            ]);
        }

        return new Hotspot();
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        $serialized = [];

        if ($value instanceof Hotspot) {
            $serialized = [
                'x' => $value->x,
                'y' => $value->y,
            ];
        }

        return parent::serializeValue($serialized, $element);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(ImageHotspotsFieldAsset::class);

        if (strlen($this->relatedAssetHandle)) {
            if ($element instanceof \verbb\supertable\elements\SuperTableBlockElement) {
                $rootElement = $element->getOwner();
            } else {
                $rootElement = $element;
            }

            $rootElement = $this->determineFieldOwner($this->relatedAssetHandle, $element);

            if (isset($rootElement)) {
                $relatedAssetHandle = $this->relatedAssetHandle;
                $assetField = $rootElement->$relatedAssetHandle;
            }
        }

        /** @var Hotspot|null $value */
        return Craft::$app->getView()->renderTemplate('imagehotspots/_components/field/input', [
            'id' => Craft::$app->getView()->formatInputId($this->handle),
            'name' => $this->handle,
            'value' => $value,
            'relatedAssetHandle' => $this->relatedAssetHandle,
            'asset' => isset($assetField) ? $assetField->one() : null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getContentGqlType() {
        return HotspotType::getType();
    }

    /**
     * Determine the owner of a field, by looping through the parents.
     * 
     * @param string $fieldHandle
     * @param ElementInterface $element
     * @return ElementInterface
     */
    private function determineFieldOwner(string $fieldHandle, ElementInterface $element = null)
    {
        if (isset($element->$fieldHandle)) {
            return $element;
        }

        // Neo block elements can be nested under a parent without them being the owner.
        if (class_exists('\benf\neo\elements\Block') && $element instanceof \benf\neo\elements\Block) {
            $parent = $element->getParent();

            if (isset($parent)) {
                return $this->determineFieldOwner($fieldHandle, $parent);
            }
        }

        return $this->hasOwner($element) ? $this->determineFieldOwner($fieldHandle, $element->getOwner()) : null;
    }

    /**
     * Determine if the element is the child of another element.
     * 
     * @param ElementInterface $element
     * @return bool
     */
    private function hasOwner($element)
    {
        return (
            (
                class_exists('\verbb\supertable\elements\SuperTableBlockElement')
                && $element instanceof \verbb\supertable\elements\SuperTableBlockElement
            )
            || $element instanceof \craft\elements\MatrixBlock
        );
    }
}
