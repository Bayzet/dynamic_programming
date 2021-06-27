<?php
declare(strict_types=1);

namespace App;

class Backpack
{
    /** @var integer */
    private $size;

    /** @var null|Product[] */
    private $products;

    /** @var integer */
    private $freeSize;

    public function __construct(int $size)
    {
        $this->size = $size;
        $this->freeSize = $size;
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
     * @return Backpack
     */
    public function setSize(int $size): Backpack
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return null|Product[]
     */
    public function getProducts(): ?array
    {
        return $this->products;
    }

    /**
     * @param Product $product
     * @return Backpack
     */
    public function addProduct(Product $product): Backpack
    {
        if ($product->getSize() <= $this->getFreeSize()) {
            $this->products[] = $product;
            $this->freeSize -= $product->getSize();
        } else {
            throw new \Exception(sprintf(
                "Некуда ложить продукт \"%s\" с весом \"%s\", свободно \"%s\" из \"%s\"",
                $product->getName(),
                $product->getSize(),
                $this->getFreeSize(),
                $this->getSize()
            ));
        }

        return $this;
    }

    public function addProducts(array $products) {
        foreach ($products as $product) {
            $this->addProduct($product);
        }

        return $this;
    }

    public function getFullValue() {
        $fullValue = 0;
        foreach ($this->getProducts() as $product) {
            $fullValue += $product->getValue();
        }

        return $fullValue;
    }

    /**
     * @return int
     */
    public function getFreeSize(): int
    {
        return $this->freeSize;
    }

    /**
     * @param int $freeSize
     * @return Backpack
     */
    public function setFreeSize(int $freeSize): Backpack
    {
        $this->freeSize = $freeSize;
        return $this;
    }

}