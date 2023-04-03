<?php

namespace Model\Products;

use Model\Product;

class Book extends Product
{
    private string $weight;

    public function setter(string $weight)
    {
        $this->weight = $weight;
    }

    public function getter()
    {
        return $this->weight;
    }
}
