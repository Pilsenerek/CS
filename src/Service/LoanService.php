<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Loan;
use App\Form\LoanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LoanService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface   $formFactory,
        private NormalizerInterface    $normalizer,
        #[Autowire('%loanInterest%')] private int $loanInterest
    )
    {
    }

    public function createLoan(Request $request): JsonResponse
    {
        $inputData = $request->toArray();
        $form = $this->formFactory->create(LoanType::class);
        $form->submit($inputData);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $loan = new Loan($this->loanInterest, $inputData['amount'], $inputData['installments']);
                $this->entityManager->persist($loan);
                $this->entityManager->flush();

                return new JsonResponse(['loan' => $this->normalizer->normalize($loan)], Response::HTTP_CREATED);
            }

            return $this->retrieveErrors($form);
        }

        return new JsonResponse(['Wrong form request'], Response::HTTP_BAD_REQUEST);
    }

    private function retrieveErrors(Form $form): JsonResponse
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }

}
