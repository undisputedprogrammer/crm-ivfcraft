<?php
namespace Ynotz\EasyAdmin\Services;

use Ynotz\EasyAdmin\Contracts\LayoutElementInterface;
use Ynotz\EasyAdmin\Exceptions\InvalidLayoutFormatException;

class LayoutElement implements LayoutElementInterface
{
    protected $type;
    protected $width;
    protected $style;
    protected $elements = [];
    protected $properties = [];

    public function __construct(string $width = null, string $style = '')
    {
        $this->width = $width ?? 'full';
        $this->style = $style;
    }

    public function getLayout(): array
    {
        return [
            'item_type' => 'layout',
            'layout_type' => $this->type,
            'width' => $this->width,
            'style' => $this->style,
            'items' => $this->elements,
            'properties' => $this->properties
        ];
    }

    public function addElement(LayoutElement $el): LayoutElement
    {
        if (!$this->hasInputElement()) {
            $this->elements[] = $el->getLayout();
        } else {
            throw new InvalidLayoutFormatException("Can't add layout element to the " . $this->type .". The " . $this->type . " already has an input slot.");
        }
        return $this;
    }

    public function addElements(array $elements): LayoutElement
    {
        foreach ($elements as $el) {
            if (!$this->hasInputElement()) {
                $this->elements[] = $el->getLayout();
            } else {
                throw new InvalidLayoutFormatException("Can't add layout element to the " . $this->type .". The " . $this->type . " already has an input slot.");
            }
        }
        return $this;
    }

    public function addInputSlot(
        string $key
    ): LayoutElement {
        $this->elements[] = [
            'item_type' => 'input',
            'key' => $key
        ];
        return $this;
    }

    public function addInputSlots(
        array $keys
    ): LayoutElement {
        foreach ($keys as $key) {
            $this->elements[] = [
                'item_type' => 'input',
                'key' => $key
            ];
        }
        return $this;
    }

    private function hasInputElement(): bool
    {
        $status = false;
        foreach ($this->elements as $el) {
            if ($el['item_type'] == 'input') {
                $status = true;
                break;
            }
        }
        return $status;
    }

    public function setWidth(string $width): LayoutElement
    {
        $this->width = $width;
        return $this;
    }

    public function setProperty(string $key, string $value): LayoutElement
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function unsetProperty(string $key): LayoutElement
    {
        unset($this->properties[$key]);
        return $this;
    }
}
?>
