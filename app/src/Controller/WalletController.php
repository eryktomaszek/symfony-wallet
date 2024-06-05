<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class WalletController.
 */
#[Route('/wallet')]
class WalletController extends AbstractController
{
    /**
     * Index action.
     *
     * @param WalletRepository $walletRepository Wallet repository
     * @param PaginatorInterface $paginator Paginator
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'wallet_index',
        methods: 'GET'
    )]
    public function index(WalletRepository $walletRepository, PaginatorInterface $paginator, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $walletRepository->queryAll(),
            $page,
            WalletRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('wallet/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Wallet $wallet Wallet entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'wallet_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[ParamConverter('wallet', class: 'App\Entity\Wallet')]
    public function show(Wallet $wallet): Response
    {
        return $this->render(
            'wallet/show.html.twig',
            ['wallet' => $wallet]
        );
    }
}
