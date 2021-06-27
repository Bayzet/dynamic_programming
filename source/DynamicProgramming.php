<?php
declare(strict_types=1);

namespace App;

class DynamicProgramming
{
    private int $fullSize;
    private int $minObjectSize;
    private array $products;
    private string $best_result_filename;

    public function __construct(int $fullSize, int $minObjectSize, array $dataset, string $datasetName)
    {
        $this->fullSize = $fullSize;
        $this->minObjectSize = $minObjectSize;
        $this->best_result_filename = __DIR__ . "/best_result_" . $fullSize . "_" . $minObjectSize . "_" . $datasetName;
        $this->products = $this->createProducts($dataset, file_exists($this->best_result_filename));
    }

    private function createProducts(array $dataset, bool $bestResultExist = false): array
    {
        $products = [];
        foreach ($dataset as $data) {
            if ((isset($data['processed']) && !$data['processed']) || !$bestResultExist) {
                $products[] = new Product(
                    $data['name'],
                    $data['size'],
                    (int) $data['value']
                );
            }
        }

        return $products;
    }

    private function algorithm(Backpack $newBackpack, Product $product, array $bestBackpack = []): void
    {
        $isSizeProductLessOrEqualSizeBackpack = $product->getSize() <= $newBackpack->getSize();
        $prevBestBackpack = $bestBackpack['prev'][$newBackpack->getSize()] ?? null;
        // Если в списке лучших портфелей нет подходящего нам размера, то проверяем и кладём переданный продукт
        if ($isSizeProductLessOrEqualSizeBackpack && is_null($prevBestBackpack)) {
            $newBackpack->addProduct($product);
        } elseif ($isSizeProductLessOrEqualSizeBackpack) { // если всё-же есть портфель
            $valuePrevBestBackpackForCurrentSize = $prevBestBackpack->getFullValue() ?? 0;
            $prevBestBackpackForFreeSize = $bestBackpack['prev'][$newBackpack->getSize() - $product->getSize()] ?? null;
            $valuePrevBestBackpackForFreeSize = is_null($prevBestBackpackForFreeSize) ? 0 : $prevBestBackpackForFreeSize->getFullValue();
            $sumValueProductAndPrevBestBackpackForFreeSize = $product->getValue() + $valuePrevBestBackpackForFreeSize;
            // Если ценность текущего продукта + ценность портфеля на оставшееся место больше известного нам лучшего портфеля для размера
            if ($sumValueProductAndPrevBestBackpackForFreeSize > $valuePrevBestBackpackForCurrentSize) {
                if ($valuePrevBestBackpackForFreeSize > 0) {
                    $newBackpack
                        ->addProduct($product)
                        ->addProducts($prevBestBackpackForFreeSize->getProducts());
                } else {
                    $newBackpack
                        ->addProduct($product);
                }
            } else {
                $newBackpack->addProducts($prevBestBackpack->getProducts());
            }
        } else {
            if (!is_null($prevBestBackpack)) {
                $newBackpack->addProducts($prevBestBackpack->getProducts());
            }
        }
    }

    public function process(): array
    {
        $bestBackpacks = [
            'prev' => [],
            'current' => $this->getSaveResult()
        ];

        foreach ($this->products as $product) {
            $availableProducts[] = $product;
            $bestBackpacks['prev'] = $bestBackpacks['current'];
            $bestBackpacks['current'] = [];
            for ($currentSize = $this->minObjectSize; $currentSize <= $this->fullSize; $currentSize += $this->minObjectSize) {
                $newBackpack = new Backpack($currentSize);

                $this->algorithm($newBackpack, $product, $bestBackpacks);
                $bestBackpacks['current'][$currentSize] = $newBackpack->getProducts() ? $newBackpack : null;
            }
        }

        return $bestBackpacks['current'];
    }

    /**
     * @return int
     */
    public function getFullSize(): int
    {
        return $this->fullSize;
    }

    /**
     * @return int
     */
    public function getCountProducts(): int
    {
        return count($this->products);
    }

    public function saveResult(array $bestBackpacks) {
        $serialize = serialize($bestBackpacks);
        file_put_contents($this->best_result_filename, $serialize);
    }

    private function getSaveResult()
    {
        $result = @file_get_contents($this->best_result_filename);
        if ($result) {
            return unserialize($result, ['allowed_classes' => true]);
        }

        return [];
    }
}