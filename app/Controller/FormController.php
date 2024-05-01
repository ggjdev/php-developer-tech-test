<?php

namespace App\Controller;

use App\Service\CompanyMatcher;

class FormController extends Controller
{
    public function index()
    {
        $this->render('form.twig');
    }

    public function submit()
    {
        $errors = $this->validateRequest();
        if (!empty($errors)) {
            $this->render('form.twig', [
                'errors' => $errors,
            ]);
            return;
        }

        $matcher = new CompanyMatcher($this->db());

        $matcher->match(
            $_POST['postcode'],
            $_POST['bedrooms'],
            $_POST['survey_type'],
            $this->getMaxMatchedCompanies()
        );

        $matcher->deductCredits();

        $this->render('results.twig', [
            'matchedCompanies'  => $matcher->results(),
        ]);
    }

    private function validateRequest(): array
    {
        $errors = [];

        if (
            !isset($_POST['postcode']) ||
            empty($_POST['postcode']) ||
            !preg_match('/^[A-Z]{1,2}[A-Z0-9 ]+$/i', $_POST['postcode'])
        ) {
            $errors[] = 'A valid postcode is required';
        }

        if (!isset($_POST['bedrooms']) || !is_numeric($_POST['bedrooms'])) {
            $errors[] = 'A valid number of bedrooms is required';
        }

        $survey_types = ['homebuyer', 'valuation', 'building'];
        if (!isset($_POST['survey_type']) || !in_array($_POST['survey_type'], $survey_types)) {
            $errors[] = 'A valid survey type is required';
        }

        return $errors;
    }

    private function getMaxMatchedCompanies(): int
    {
        $maxMatchedCompanies = (int) $_ENV['MAX_MATCHED_COMPANIES'];

        if ($maxMatchedCompanies < 1) {
            $maxMatchedCompanies = 3;
        }

        return $maxMatchedCompanies;
    }
}
