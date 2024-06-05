<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class TransactionController.
 */
#[Route('/transaction')]
class TransactionController extends AbstractController
{
    /**
     * Index action.
     *
     * @param TransactionRepository $transactionRepository Transaction repository
     * @param PaginatorInterface $paginator Paginator
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'transaction_index',
        methods: 'GET'
    )]
    public function index(TransactionRepository $transactionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $transactionRepository->queryAll(),
            $request->query->getInt('page', 1),
            TransactionRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('transaction/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'transaction_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[ParamConverter('transaction', class: 'App\Entity\Transaction')]
    public function show(Transaction $transaction): Response
    {
        return $this->render(
            'transaction/show.html.twig',
            ['transaction' => $transaction]
        );
    }
}
