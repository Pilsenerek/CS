<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Loan;
use App\Enum\LoanStatus;
use App\Form\LoanType;
use App\Model\Installment;
use App\Model\InstallmentResponse;
use App\Model\LoanResponse;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LoanService
{
    private const CALCULATIONS_LIMIT = 4;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface   $formFactory,
        private NormalizerInterface    $normalizer,
        private LoanCalcService        $calcService,
        private LoanRepository $loanRepository,
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

                return new JsonResponse($this->prepareResponse($loan, $installments), Response::HTTP_CREATED);
            }

            return $this->retrieveErrors($form);
        }

        return new JsonResponse(['Wrong form request'], Response::HTTP_BAD_REQUEST);
    }

    public function excludeLoan(int $loanId): JsonResponse
    {
        /* @var $loan Loan */
        $loan = $this->loanRepository->find($loanId);
        if (empty($loan)) {

            return new JsonResponse([], Response::HTTP_NOT_FOUND);
        }
        $loan->setStatus(LoanStatus::EXCLUDED);
        $this->entityManager->flush();

        return new JsonResponse([]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function list(Request $request): JsonResponse
    {
        $returnData = [];
        $criteria = [];
        //@todo implement Form & validation
        $status = $request->get('status');
        if (\in_array($status, ['0', '1'])) {
            $criteria = ['status' => LoanStatus::from((int)$status)];
        }
        $loans = $this->loanRepository->findBy($criteria, ['id' => 'DESC'], self::CALCULATIONS_LIMIT);
        \usort($loans, function ($a, $b) {
            return $b->getInterestAmount() <=> $a->getInterestAmount();
        });
        foreach ($loans as $loan) {
            $returnData[] = $this->prepareResponse($loan, $this->calcService->calculateInstallments($loan));
        }

        return new JsonResponse($returnData);
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
     * @param Installment[] $installments
     * @throws ExceptionInterface
     */
    private function prepareResponse(Loan $loan, array $installments): array
    {
        return [
            'loan' => $this->normalizer->normalize(LoanResponse::createFromLoan($loan)),
            'schedule' => $this->normalizer->normalize($this->prepareInstallmentResponse($installments)),
        ];
    }

    /**
     * @param Installment[] $installments
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

    private function retrieveErrors(FormInterface $form): JsonResponse
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
    }
}
