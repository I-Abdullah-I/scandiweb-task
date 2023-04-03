<?php

namespace Database;

class Database
{
    protected $connection = null;

    public function __construct()
    {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->connection = new \mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
    	
            if ( mysqli_connect_errno()) {
                throw new \Exception("Could not connect to database.");   
            }
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());   
        }			
    }

    public function insert(string $query)
    {
        try {
            $stmt = $this->executeStatement($query);
            $stmt->close();
            return $this->connection->insert_id;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return false;
    }
    
    public function delete(string $query)
    {
        try {
            $stmt = $this->executeStatement($query);
            $stmt->close();
            return true;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return false;
    }

    public function select(string $query)
    {
        try {
            $stmt = $this->executeStatement($query);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);				
            $stmt->close();
            $records = array();
            foreach ($result as $record) {
                $record["attributes"] = json_decode($record["attributes"], true);
                array_push($records, $record);
            }
            return $records;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return false;
    }

    private function executeStatement(string $query)
    {
        try {
            $stmt = $this->connection->prepare($query);
            if($stmt === false) {
                throw New \Exception("Unable to do prepared statement: " . $query);
            }
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }
            return $stmt;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }	
    }
}