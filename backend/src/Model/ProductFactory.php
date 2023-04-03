<?php

namespace Model;

class ProductFactory
{
    public function createProduct(string $productType): Product
    {
        $productInstance = "Model\\Products\\" . ucwords($productType);

        if (!class_exists($productInstance)) {
            throw new \Exception("A class with the name " . $productInstance . " could not be found!");
        }

        return new $productInstance();
    }
}
?>