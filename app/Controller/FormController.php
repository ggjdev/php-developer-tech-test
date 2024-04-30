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

    private function getMaxMatchedCompanies(): int
    {
        $maxMatchedCompanies = (int) $_ENV['MAX_MATCHED_COMPANIES'];

        if ($maxMatchedCompanies < 1) {
            $maxMatchedCompanies = 3;
        }

        return $maxMatchedCompanies;
    }
}
