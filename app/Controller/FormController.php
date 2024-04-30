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

        $this->render('results.twig', [
            'matchedCompanies'  => $matchedCompanies,
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
