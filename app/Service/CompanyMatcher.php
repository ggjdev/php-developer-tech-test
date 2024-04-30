<?php

namespace App\Service;

class CompanyMatcher
{
    private $db;
    private $matches = [];

    public function __construct(\PDO $db) 
    {
        $this->db = $db;
    }

    public function match()
    {
        
    }

    public function pick(int $count)
    {
        
    }

    public function results(): array
    {
        return $this->matches;
    }

    public function deductCredits()
    {
        
    }

    private function getPostcodePrefix(string $postcode): string
    {
        preg_match(
            '/^([A-Z]{1,2})/',
            strtoupper($postcode),
            $matches
        );

        return isset($matches[1]) ? $matches[1] : '';
    }
}
