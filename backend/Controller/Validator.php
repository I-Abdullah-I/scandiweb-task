<?php

namespace Controller;

class Validator
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function validate(array $data): bool
    {
        foreach ($this->keys as $key) {
            try {
                if (!array_key_exists($key, $data)) {
                    throw new \Exception("Data must include " . $key);
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());   
            }
        }
        return true;
    }
}

?>