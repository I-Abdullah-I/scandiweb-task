<?php

namespace Model;

use Database\Database;

abstract class Product
{
    protected array $data;
    private $session;

    public function __construct()
    {
        $this->session = new Database();
    }

    public function setProductData($data)
    {
        $this->data = $data;
    }
    
    public function addProduct()
    {
        try {            
            $entityQuery = sprintf('INSERT INTO eav_product_catalog (name, price, sku, type) VALUES ("%s", %f, "%s", "%s")', $this->data["name"], $this->data["price"], $this->data["sku"], $this->data["type"]);
            $productId = $this->session->insert($entityQuery);
            
            foreach($this->data["attributes"] as $attribute => $value) {
                $attributeQuery = sprintf('INSERT INTO eav_product_attribute_value_numeric (attribute_id, entity_id, attribute_value) VALUES (
                        (SELECT id FROM eav_attribute WHERE label = "%s"),
                        (SELECT id FROM eav_product_catalog WHERE sku = "%s"),
                        %f)', 
                $attribute, $this->data["sku"], $value);
                $this->session->insert($attributeQuery);
            }

            $addedProductInList = $this->selectById([$productId]);
            $addedProduct = array_pop($addedProductInList);
            return $addedProduct;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    public function readProductCatalog()
    {
        try{
            $fetchAllQuery = 'SELECT P.id, P.sku, P.name, P.price, P.type, JSON_OBJECTAGG(A.label, V.attribute_value) AS attributes
            FROM eav_product_catalog AS P 
            LEFT JOIN eav_product_attribute_value_numeric AS V 
            ON P.id = V.entity_id
            LEFT JOIN eav_attribute AS A
            ON A.id = V.attribute_id
            GROUP BY P.sku;';

            $productList = $this->session->select($fetchAllQuery);
            
            return $productList;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function massDelete($productIds)
    {
        try{
            $deletedProducts = $this->selectById($productIds);
            $this->deleteById($productIds);
            return $deletedProducts;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function selectById(array $productIds)
    {
        try {
            $selectQuery = "SELECT P.id, P.sku, P.name, P.price, P.type, JSON_OBJECTAGG(A.label, V.attribute_value) AS attributes
                FROM eav_product_catalog AS P 
                LEFT JOIN eav_product_attribute_value_numeric AS V 
                ON P.id = V.entity_id
                LEFT JOIN eav_attribute AS A
                ON A.id = V.attribute_id
                WHERE P.id IN (";
            $selectQueryTrailer = " GROUP BY P.sku;";
            
            foreach ($productIds as $id) {
                $selectQuery = $selectQuery . " {$id},";
            }
    
            $selectQuery = rtrim($selectQuery, ",") . " )" . $selectQueryTrailer;
            // print_r($selectQuery);
            $records = $this->session->select($selectQuery);
            
            return $records;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    public function selectBySku(array $productSkus)
    {
        try {
            $selectQuery = "SELECT P.id, P.sku, P.name, P.price, P.type, JSON_OBJECTAGG(A.label, V.attribute_value) AS attributes
                FROM eav_product_catalog AS P 
                LEFT JOIN eav_product_attribute_value_numeric AS V 
                ON P.id = V.entity_id
                LEFT JOIN eav_attribute AS A
                ON A.id = V.attribute_id
                WHERE P.sku IN (";
            $selectQueryTrailer = " GROUP BY P.sku;";
            
            foreach ($productSkus as $sku) {
                $selectQuery = $selectQuery . " \"{$sku}\",";
            }
    
            $selectQuery = rtrim($selectQuery, ",") . " )" . $selectQueryTrailer;
            // print_r($selectQuery);
            $records = $this->session->select($selectQuery);
            
            if (count($records) === 1)
            {
                return array_pop($records);
            }

            return $records;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteById(array $productIds)
    {
        try{
            $massDeleteQuery = 'DELETE FROM eav_product_catalog WHERE id IN (';
            foreach ($productIds as $id) {
                $massDeleteQuery = $massDeleteQuery . " {$id},";
            }
            $massDeleteQuery = rtrim($massDeleteQuery, ",") . " );";

            $this->session->delete($massDeleteQuery);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
