<?php

namespace App\Service;

use PDO;

class CompanyMatcher
{
    private $db;
    private $matches = [];

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function match(string $postcode, int $bedrooms, string $surveyType, int $count = 3)
    {
        $postcode = $this->getPostcodePrefix($postcode);

        $postcode = "%\"$postcode\"%";
        $bedrooms = "%\"$bedrooms\"%";

        $query = $this->db->prepare(
            'SELECT
                `companies`.`id`,
                `companies`.`name`,
                `companies`.`description`,
                `companies`.`email`,
                `companies`.`phone`,
                `companies`.`website`
            FROM `company_matching_settings`
            INNER JOIN `companies`
            ON `company_matching_settings`.`company_id` = `companies`.`id`
            WHERE `company_matching_settings`.`postcodes` LIKE :postcode
            AND `company_matching_settings`.`bedrooms` LIKE :bedrooms
            AND `company_matching_settings`.`type` = :survey_type
            AND `companies`.`credits` > 0
            AND `companies`.`active` = 1
            LIMIT :count'
        );

        $query->bindParam(':postcode', $postcode);
        $query->bindParam(':bedrooms', $bedrooms);
        $query->bindParam(':survey_type', $surveyType);
        $query->bindParam(':count', $count, PDO::PARAM_INT);

        $query->execute();

        $this->matches = $query->fetchAll(PDO::FETCH_ASSOC);
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
