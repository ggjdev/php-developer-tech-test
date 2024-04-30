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
}
