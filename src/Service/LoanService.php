<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Loan;
use App\Form\LoanType;
use App\Model\Installment;
use App\Model\InstallmentResponse;
use App\Model\LoanResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LoanService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface   $formFactory,
        private NormalizerInterface    $normalizer,
        private LoanCalcService        $calcService,
        #[Autowire('%loanInterest%')] private int $loanInterest
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function createLoan(Request $request): JsonResponse
    {
        $inputData = $request->toArray();
        $form = $this->formFactory->create(LoanType::class);
        $form->submit($inputData);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $loan = new Loan($this->loanInterest, $inputData['amount'] * 100, $inputData['installments']);
                $installments = $this->calcService->calculateInstallments($loan);
                $this->sumInterest($loan, $installments);
                $this->entityManager->persist($loan);
                $this->entityManager->flush();

                return $this->prepareResponse($loan, $installments);
            }

            return $this->retrieveErrors($form);
        }

        return new JsonResponse(['Wrong form request'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Loan $loan
     * @param Installment[] $installments
     */
    private function sumInterest(Loan $loan, array $installments): void
    {
        foreach ($installments as $installment) {
            $loan->setInterestAmount($loan->getInterestAmount() + $installment->getInterest());
        }
    }

    /**
     * @param Loan $loan
     * @param Installment[]
     * @throws ExceptionInterface
     */
    private function prepareResponse(Loan $loan, array $installments): JsonResponse
    {
        return new JsonResponse([
            'loan' => $this->normalizer->normalize(LoanResponse::createFromLoan($loan)),
            'schedule' => $this->normalizer->normalize($this->prepareInstallmentResponse($installments)),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Installment[]
     * @return InstallmentResponse[]
     */
    private function prepareInstallmentResponse(array $installments): array
    {
        $result = [];
        foreach ($installments as $installment) {
            $result[] = InstallmentResponse::createFromInstallment($installment);
        }

        return $result;
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
