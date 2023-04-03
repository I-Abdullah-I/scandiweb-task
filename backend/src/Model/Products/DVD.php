<?php

namespace Model\Products;

use Model\Product;

class DVD extends Product
{
    private string $size;

    public function setter(string $size)
    {
        $this->size = $size;
    }

    public function getter()
    {
        return $this->size;
    }
}

?>