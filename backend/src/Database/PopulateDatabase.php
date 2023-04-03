<?php

namespace Database;

use Model\Product;

class PopulateDatabase
{
    public function populateFromJsonFile()
    {
        $filePath = PROJECT_ROOT_PATH . "/initialData.json";
        $contentAsString = file_get_contents($filePath);
        $items = json_decode($contentAsString, true);

        foreach($items as $item) {
            try {
                $session = new Database();
                
                $entityQuery = sprintf('INSERT INTO eav_product_catalog (name, price, sku, type) VALUES ("%s", %f, "%s", "%s")', $item["name"], $item["price"], $item["sku"], $item["type"]);
                $session->insert($entityQuery);
                
                foreach($item["attributes"] as $attribute => $value) {
                    $attributeQuery = sprintf('INSERT INTO eav_product_attribute_value_numeric (attribute_id, entity_id, attribute_value) VALUES (
                            (SELECT id FROM eav_attribute WHERE label = "%s"),
                            (SELECT id FROM eav_product_catalog WHERE sku = "%s"),
                            %f)', 
                    $attribute, $item["sku"], $value);
                    $session->insert($attributeQuery);
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}