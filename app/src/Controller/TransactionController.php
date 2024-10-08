<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\Type\TransactionType;
use App\Service\CategoryServiceInterface;
use App\Service\TagServiceInterface;
use App\Service\TransactionServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TransactionController.
 *
 * Controller responsible for handling transaction-related actions such as
 * listing, creating, editing, and deleting transactions.
 */
#[\Symfony\Component\Routing\Attribute\Route('/transaction')]
class TransactionController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TransactionServiceInterface $transactionService Transaction service
     * @param CategoryServiceInterface    $categoryService    Category service
     * @param TagServiceInterface         $tagService         Tag service
     * @param TranslatorInterface         $translator         Translator service
     */
    public function __construct(private readonly TransactionServiceInterface $transactionService, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action - displays a paginated list of transactions for the current user.
     *
     * @param Request            $request   HTTP Request object
     * @param PaginatorInterface $paginator Pagination mechanism
     *
     * @return Response HTTP Response object
     *
     * @throws \DateMalformedStringException
     */
    #[\Symfony\Component\Routing\Attribute\Route(name: 'transaction_index', methods: 'GET')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->addFlash('error', $this->translator->trans('message.user_not_found'));

            return $this->redirectToRoute('app_login');
        }

        $categoryId = $request->query->get('categoryId');
        $categoryId = empty($categoryId) ? null : (int) $categoryId;

        $tags = $request->query->all('tags');
        $startDate = $request->query->get('startDate') ? new \DateTime($request->query->get('startDate')) : null;
        $endDate = $request->query->get('endDate') ? new \DateTime($request->query->get('endDate')) : null;

        $transactionsQuery = $this->transactionService->getFilteredTransactionsQuery(
            $user,
            $categoryId,
            $tags,
            $startDate,
            $endDate
        );

        $pagination = $paginator->paginate(
            $transactionsQuery,
            $request->query->getInt('page', 1),
            10
        );

        $categories = $this->categoryService->getAllCategories();
        $allTags = $this->tagService->getAllTags();

        return $this->render('transaction/index.html.twig', [
            'pagination' => $pagination, // Paginated transactions
            'categories' => $categories,
            'selectedCategoryId' => $categoryId,
            'tags' => $allTags,
            'selectedTags' => $tags,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Show action - displays a specific transaction.
     *
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP Response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'transaction_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', ['transaction' => $transaction]);
    }

    /**
     * Create action - handles the creation of a new transaction.
     *
     * @param Request $request HTTP Request object
     *
     * @return Response HTTP Response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/create', name: 'transaction_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->transactionService->save($transaction);
                $this->addFlash('success', $this->translator->trans('message.created_successfully'));

                return $this->redirectToRoute('transaction_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('transaction/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action - handles editing of an existing transaction.
     *
     * @param Request     $request     HTTP Request object
     * @param Transaction $transaction Transaction entity to be edited
     *
     * @return Response HTTP Response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/edit', name: 'transaction_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->transactionService->save($transaction);
                $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

                return $this->redirectToRoute('transaction_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete action - handles the deletion of a transaction.
     *
     * @param Request     $request     HTTP Request object
     * @param Transaction $transaction Transaction entity to be deleted
     *
     * @return Response HTTP Response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'transaction_delete', requirements: ['id' => '[1-9]\d*'], methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $this->transactionService->delete($transaction);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));
        } else {
            $this->addFlash('error', $this->translator->trans('message.delete_failed'));
        }

        return $this->redirectToRoute('transaction_index');
    }
}
