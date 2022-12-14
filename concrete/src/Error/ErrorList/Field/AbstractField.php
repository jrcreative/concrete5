<?php
namespace Concrete\Core\Error\ErrorList\Field;

abstract class AbstractField implements FieldInterface
{

    public function __toString()
    {
        return $this->getDisplayName();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ['element' => $this->getFieldElementName(), 'name' => $this->getDisplayName()];
    }


}
