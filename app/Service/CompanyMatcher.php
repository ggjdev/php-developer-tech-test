<?php

namespace App\Service;

use App\Logger;
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
                `companies`.`website`,
                RAND() as `random_order`
            FROM `company_matching_settings`
            INNER JOIN `companies`
            ON `company_matching_settings`.`company_id` = `companies`.`id`
            WHERE `company_matching_settings`.`postcodes` LIKE :postcode
            AND `company_matching_settings`.`bedrooms` LIKE :bedrooms
            AND `company_matching_settings`.`type` = :survey_type
            AND `companies`.`credits` > 0
            AND `companies`.`active` = 1
            ORDER BY `random_order`
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

    public function deductCredits(): void
    {
        $companyIds = array_column($this->matches, 'id');

        $query = $this->db->prepare(
            sprintf(
                'UPDATE `companies`
                SET `credits` = `credits` - 1
                WHERE `id` IN (%s)',
                implode(',', array_fill(0, count($companyIds), '?'))
            )
        );

        $query->execute($companyIds);

        $this->logCompaniesWithZeroCredits($companyIds);
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

    private function logCompaniesWithZeroCredits(array $companyIds): void
    {
        $query = $this->db->prepare(
            sprintf(
                'SELECT `id`, `name`
                FROM `companies`
                WHERE `credits` = 0
                AND `id` IN (%s)',
                implode(',', array_fill(0, count($companyIds), '?'))
            )
        );

        $query->execute($companyIds);

        $companies = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($companies as $company) {
            Logger::warning(
                "Company {$company['name']} has run out of credits.",
                $company
            );
        }
    }
}
