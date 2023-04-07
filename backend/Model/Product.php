<?php

namespace Model;

use Database\Database;

abstract class Product
{
    /**
     * @var Database an instance of Database object 
     */
    private Database $session;

    /**
     * Establishes connection with the database and saves the instance in $session
     */
    public function __construct()
    {
        $this->session = new Database();
    }
    
    /**
     * Inserts a product into the database. At first, the entity table is popualted with incoming data.
     * Then, product attributes are populated 
     * 
     * @param array $data
     * @return array $addedProduct
     * @throws Exception in case adding the product failed
     */
    public function addProduct(array $data)
    {
        try {            
            $entityQuery = sprintf('INSERT INTO eav_product_catalog (name, price, sku, type) VALUES ("%s", %f, "%s", "%s")', $data["name"], $data["price"], $data["sku"], $data["type"]);
            $productId = $this->session->insert($entityQuery);
            
            foreach($data["attributes"] as $attribute => $value) {
                $attributeQuery = sprintf('INSERT INTO eav_product_attribute_value_numeric (attribute_id, entity_id, attribute_value) VALUES (
                    (SELECT id FROM eav_attribute WHERE label = "%s"),
                    (SELECT id FROM eav_product_catalog WHERE sku = "%s"),
                    %f)', 
                    $attribute, $data["sku"], $value);
                $this->session->insert($attributeQuery);
            }

            $addedProductInList = $this->selectById([$productId]);
            $addedProduct = array_pop($addedProductInList);
            return $addedProduct;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
        
    /**
     * Reads all records from the entity table joined on the attribute table of the respective entity.
     * In our situation it's the product entity
     * 
     * @return array<array> $productList
     * @throws Exception in case products selection failed
     */
    public function readProductCatalog()
    {
        try{
            $fetchAllQuery = 'SELECT P.id, P.sku, P.name, P.price, P.type, JSON_OBJECTAGG(A.label, V.attribute_value) AS attributes
            FROM eav_product_catalog AS P 
            LEFT JOIN eav_product_attribute_value_numeric AS V 
            ON P.id = V.entity_id
            LEFT JOIN eav_attribute AS A
            ON A.id = V.attribute_id
            GROUP BY P.sku
            ORDER BY P.id DESC;';

            $productList = $this->session->select($fetchAllQuery);
            
            return $productList;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Selects the products that should be deleted then deletes them
     * 
     * @param array<int> $productIds
     * @return array<array> $deletedProducts
     * @throws Exception in case selection of deletion fails 
     */
    public function massDelete(array $productIds)
    {
        try{
            $deletedProducts = $this->selectById($productIds);
            $this->deleteById($productIds);
            return $deletedProducts;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Selects products by id. The query is made dynamic to account for different array sizes
     * 
     * @param array<int> $productIds
     * @return array<array> $productList
     * @throws Exception in case selection fails
     */
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
            $productList = $this->session->select($selectQuery);
            
            return $productList;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * Selects products by sku. The query is made dynamic to account for different array sizes
     * 
     * @param array<int> $productSkus
     * @return array<array> $productList
     * @throws Exception in case selection fails
     */
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
            $productList = $this->session->select($selectQuery);
            
            if (count($productList) === 1)
            {
                return array_pop($productList);
            }

            return $productList;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Deletes products by id. The query is made dynamic to account for different array sizes
     * 
     * @param array<int> $productIds
     * @throws Exception in case deletion fails
     */

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
