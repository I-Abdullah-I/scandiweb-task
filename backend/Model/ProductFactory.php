<?php

namespace Model;

class ProductFactory
{
    /**
     * Dynamic product instantiation with the respective product type passed as an argument.
     * 
     * @param string $productType
     * @return Product
     * @throws Exception if no matching type is found
     */
    public function createProduct(string $productType): Product
    {
        $productInstance = "Model\\Products\\" . ucwords($productType);

        if (!class_exists($productInstance)) {
            throw new \Exception("A class with the name " . $productInstance . " could not be found!");
        }

        return new $productInstance();
    }
}
