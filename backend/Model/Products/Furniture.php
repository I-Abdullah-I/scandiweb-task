<?php

namespace Model\Products;

use Model\Product;

class Furniture extends Product
{
    private string $height;
    private string $width;
    private string $length;

    public function setter(string $height)
    {
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getter()
    {
        return array( $this->height, $this->width, $this->length );
    }
}
