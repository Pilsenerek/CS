<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LoanController extends AbstractController
{

    #[Route('/loan/calculate', name: 'loan_calculate_list', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function list(): JsonResponse
    {
        return $this->json([__METHOD__]);
    }

    #[Route('/loan/calculate', name: 'loan_calculate_add', methods: ['POST'])]
    public function add(): JsonResponse
    {
        return $this->json([__METHOD__]);
    }

    #[Route('/loan/calculate/{loanId<\d+>}/status', name: 'loan_calculate_status', methods: ['PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function status(Request $request): JsonResponse
    {
        return $this->json([__METHOD__ => $request->get('loanId')]);
    }
}
