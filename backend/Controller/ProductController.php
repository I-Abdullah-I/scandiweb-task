<?php

namespace Controller;

use Database\Session;
use Database\PopulateDatabase;
use Model\ProductFactory;
use Model\Product;
use Model\Products\ProductUtils;

class ProductController extends BaseController
{
    /**
     * @var string holds error description
     */
    private string $strErrorDesc;
    
    /**
     * @var string holds error headers
     */
    private string $strErrorHeader;
    
    /**
     * @var string holds request method
     */
    private string $requestMethod;
    
    /**
     * @var mixed holds response data
     */
    private mixed $responseData;
    
    /**
     * @var mixed holds incoming data
     */
    private mixed $payload;
    
    /**
     * Initializes important properties that will be used by class members
     */
    public function __construct()
    {
        $this->strErrorDesc = '';
        $this->strErrorHeader = '';
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->payload = json_decode(file_get_contents("php://input"), true);
    }

    /**
     * Populates database
     */
    public function populateAction()
    {
        if (strtoupper($this->requestMethod) == 'GET') {
            try {
                $populate = new PopulateDatabase();
                $populate->populateFromJsonFile();
                $this->responseData = '{"message": "Database populated successfully!"}';
            } catch (\Exception $e) {
                $this->strErrorDesc = $e->getMessage();
                $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $this->strErrorDesc = 'Method not supported';
            $this->strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        if (!$this->strErrorDesc) {
            $this->sendOutput(
                $this->responseData,
                array('Content-Type: application/json', 'HTTP/1.1 201 CREATED')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $this->strErrorDesc)), 
                array('Content-Type: application/json', $this->strErrorHeader)
            );
        }
    }

    /**
     * Adds a new product to the database
     */
    public function addAction()
    {
        if (strtoupper($this->requestMethod) == 'POST') {
            try {
                $productType = $this->payload["type"];
                $productFactoryInstance = new ProductFactory();
                $productInstance = $productFactoryInstance->createProduct($productType);
                $addedProduct = $productInstance->addProduct($this->payload);
                $this->responseData = json_encode($addedProduct);
            } catch (\Exception $e) {
                $this->strErrorDesc = $e->getMessage();
                $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $this->strErrorDesc = 'Method not supported';
            $this->strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        if (!$this->strErrorDesc) {
            $this->sendOutput(
                $this->responseData,
                array('Content-Type: application/json', 'HTTP/1.1 201 CREATED')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $this->strErrorDesc)), 
                array('Content-Type: application/json', $this->strErrorHeader)
            );
        }
    }
    
    /**
     * Mass deletes products
     */
    public function massDeleteAction()
    {
        if (strtoupper($this->requestMethod) == 'DELETE') {
            try {
                if (is_array($this->payload)) {
                    $productUtils = new ProductUtils();
                    $deletedProductsList = $productUtils->massDelete($this->payload);
                    $this->responseData = json_encode($deletedProductsList);
                } else {
                    throw new \Exception("Request Body is missing.");
                }
            } catch (\Exception $e) {
                $this->strErrorDesc = $e->getMessage();
                $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $this->strErrorDesc = 'Method not supported';
            $this->strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        if (!$this->strErrorDesc) {
            $this->sendOutput(
                $this->responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $this->strErrorDesc)), 
                array('Content-Type: application/json', $this->strErrorHeader)
            );
        }
    }

    /**
     * Lists all products in database
     */
    public function listAction()
    {
        if (strtoupper($this->requestMethod) == 'GET') {
            try {
                $productUtils = new ProductUtils();
                $productList = $productUtils->readProductCatalog();
                $this->responseData = json_encode($productList, true);
            } catch (\Exception $e) {
                $this->strErrorDesc = $e->getMessage();
                $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $this->strErrorDesc = 'Method not supported';
            $this->strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        if (!$this->strErrorDesc) {
            $this->sendOutput(
                $this->responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $this->strErrorDesc)), 
                array('Content-Type: application/json', $this->strErrorHeader)
            );
        }
    }

    /**
     * Checks the existence of a product by sku
     */
    public function fetchAction()
    {
        $queryParams = $this->getQueryStringParams();
        if (strtoupper($this->requestMethod) == 'GET') {
            try {
                $productUtils = new ProductUtils();
                if (isset($queryParams["sku"])) {
                    $sku = $queryParams["sku"];
                    $fetchedProducts = $productUtils->selectBySku([$sku]);
                    $this->responseData = count($fetchedProducts);
                    $this->responseData = json_encode((count($fetchedProducts) > 0) ? true : false);
                } else {
                    throw new \Exception("Missing Query Paramter.");
                }
            } catch (\Exception $e) {
                $this->strErrorDesc = $e->getMessage();
                $this->strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $this->strErrorDesc = 'Method not supported';
            $this->strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        if (!$this->strErrorDesc) {
            $this->sendOutput(
                $this->responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $this->strErrorDesc)), 
                array('Content-Type: application/json', $this->strErrorHeader)
            );
        }
    }
}