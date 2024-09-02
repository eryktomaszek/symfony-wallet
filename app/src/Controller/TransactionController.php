<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\Type\TransactionType;
use App\Service\TransactionServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TransactionController.
 */
#[Route('/transaction')]
class TransactionController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TransactionServiceInterface $transactionService Transaction service
     * @param TranslatorInterface         $translator         Translator
     */
    public function __construct(private readonly TransactionServiceInterface $transactionService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'transaction_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->addFlash('error', $this->translator->trans('message.user_not_found'));
            return $this->redirectToRoute('app_login');
        }

        $pagination = $this->transactionService->getPaginatedList(
            $request->query->getInt('page', 1),
            $user
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
    #[IsGranted('VIEW', subject: 'transaction')]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', ['transaction' => $transaction]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'transaction_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->transactionService->save($transaction);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request     $request     HTTP request
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'transaction_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[ParamConverter('transaction', class: 'App\Entity\Transaction')]
    #[IsGranted('EDIT', subject: 'transaction')]
    public function edit(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->transactionService->save($transaction);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request     $request     HTTP request
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'transaction_delete', requirements: ['id' => '[1-9]\d*'], methods: 'POST')]
    #[ParamConverter('transaction', class: 'App\Entity\Transaction')]
    #[IsGranted('DELETE', subject: 'transaction')]
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $this->transactionService->delete($transaction);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );
        } else {
            $this->addFlash(
                'error',
                $this->translator->trans('message.delete_failed')
            );
        }

        return $this->redirectToRoute('transaction_index');
    }
}
