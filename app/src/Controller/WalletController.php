<?php

namespace App\Controller;

use App\Entity\Wallet;
use App\Form\Type\WalletType;
use App\Service\WalletServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class WalletController.
 */
#[Route('/wallet')]
class WalletController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param WalletServiceInterface $walletService Wallet service
     */
    public function __construct(private readonly WalletServiceInterface $walletService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'wallet_index', methods: 'GET')]
    public function index(Request $request, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->walletService->getPaginatedList($page);

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
        return $this->render('wallet/show.html.twig', ['wallet' => $wallet]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'wallet_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $wallet = new Wallet();
        $form = $this->createForm(WalletType::class, $wallet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletService->save($wallet);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Wallet  $wallet  Wallet entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'wallet_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[ParamConverter('wallet', class: 'App\Entity\Wallet')]
    public function edit(Request $request, Wallet $wallet): Response
    {
        $form = $this->createForm(WalletType::class, $wallet);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletService->save($wallet);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Wallet  $wallet  Wallet entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'wallet_delete', requirements: ['id' => '[1-9]\d*'], methods: 'POST')]
    #[ParamConverter('wallet', class: 'App\Entity\Wallet')]
    public function delete(Request $request, Wallet $wallet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wallet->getId(), $request->request->get('_token'))) {
            $this->walletService->delete($wallet);
        }

        $this->addFlash(
            'success',
            $this->translator->trans('message.deleted_successfully')
        );

        return $this->redirectToRoute('wallet_index');
    }
}
