<?php
declare(strict_types=1);

namespace App;

class Product
{
    /** @var string */
    private $name;

    /** @var integer */
    private $size;

    /** @var integer */
    private $value;

    /**
     * @return string
     */

    public function __construct(string $name, int $size, int $value)
    {
        $this->name = $name;
        $this->size = $size;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return product
     */
    public function setName(string $name): product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return product
     */
    public function setSize(int $size): product
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return product
     */
    public function setValue(int $value): product
    {
        $this->value = $value;
        return $this;
    }
}