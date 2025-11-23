<?php

namespace roelvanhintum\imagehotspots\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;

use craft\base\NestedElementInterface;
use craft\elements\Asset;
use craft\helpers\Json;
use GraphQL\Type\Definition\Type;
use roelvanhintum\imagehotspots\assetbundles\imagehotspotsfield\ImageHotspotsFieldAsset;
use roelvanhintum\imagehotspots\gql\HotspotType;
use roelvanhintum\imagehotspots\models\Hotspot;
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
    public ?string $relatedAssetHandle = null;

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
    public function getSettingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('imagehotspots/_components/field/settings', [
            'label' => Craft::t('imagehotspots', 'Related asset field handle'),
            'id' => 'relatedAssetHandle',
            'name' => 'relatedAssetHandle',
            'value' => $this->relatedAssetHandle,
            'errors' => is_null($this->relatedAssetHandle) ? [] : $this->getErrors('relatedAssetHandle'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['relatedAssetHandle'], 'string', 'max' => 100];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?ElementInterface $element = null): Hotspot
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
    public function serializeValue(mixed $value, ?ElementInterface $element = null): mixed
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
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(ImageHotspotsFieldAsset::class);

        $rootElement = $this->determineFieldOwner($this->relatedAssetHandle, $element);

        $asset = null;
        if ($rootElement instanceof Asset) {
            $asset = $rootElement;
        } elseif (isset($rootElement)) {
            $relatedAssetHandle = $this->relatedAssetHandle;
            $assetField = $rootElement->$relatedAssetHandle;
            $asset = isset($assetField) ? $assetField->one() : null;
        }

        /** @var Hotspot|null $value */
        return Craft::$app->getView()->renderTemplate('imagehotspots/_components/field/input', [
            'id' => Craft::$app->getView()->formatInputId($this->handle),
            'name' => $this->handle,
            'value' => $value,
            'relatedAssetHandle' => $this->relatedAssetHandle,
            'asset' => $asset,
            'index' => $this->determineElementIndex($element, $rootElement),
            'label' => $this->determineElementLabel($element),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getContentGqlType(): Type
    {
        return HotspotType::getType();
    }

    /**
     * Determine the label of the element.
     */
    private function determineElementLabel(ElementInterface|null $element = null): string|null
    {
        // Modern matrix elements just have a parent.
        if ($element instanceof ElementInterface) {
            return $element?->title ?? $element?->label ?? null;
        }

        return null;
    }

    /**
     * Determine the index of the element.
     */
    private function determineElementIndex(ElementInterface|NestedElementInterface|null $element = null, ElementInterface|null $rootElement = null): int|null
    {
        // Modern matrix elements just have a parent.
        if ($element instanceof NestedElementInterface && $element !== $rootElement) {
            return $element->getSortOrder();
        }

        return null;
    }

    /**
     * Determine the owner of a field, by looping through the parents.
     */
    private function determineFieldOwner(string $fieldHandle, ElementInterface|NestedElementInterface|null $element = null): ?ElementInterface
    {
        if ($element instanceof Asset || (strlen($fieldHandle) && isset($element->$fieldHandle))) {
            return $element;
        }

        // Neo block elements can be nested under a parent without them being the owner.
        if (class_exists('\benf\neo\elements\Block') && $element instanceof \benf\neo\elements\Block) {
            $parent = $element->getParent();

            if (isset($parent)) {
                return $this->determineFieldOwner($fieldHandle, $parent);
            }
        }

        // Modern matrix elements just have a parent.
        if ($element instanceof NestedElementInterface && $owner = $element->getOwner()) {
            return $this->determineFieldOwner($fieldHandle, $owner);
        }

        return null;
    }
}
