<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 *
 * Controller responsible for handling user management actions such as listing, creating, editing, and deleting users.
 */
#[\Symfony\Component\Routing\Attribute\Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService    User service
     * @param TranslatorInterface         $translator     Translator service
     * @param UserPasswordHasherInterface $passwordHasher Password hasher service
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Index action.
     *
     * Displays a paginated list of users.
     *
     * @param Request $request HTTP Request object
     *
     * @return Response HTTP response object
     */
    #[\Symfony\Component\Routing\Attribute\Route(name: 'user_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->userService->getPaginatedList($request->query->getInt('page', 1));

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * Displays details for a specific user.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'user_show', requirements: ['id' => '\d+'], methods: 'GET')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Create action.
     *
     * Handles the creation of a new user.
     *
     * @param Request                $request       HTTP Request object
     * @param EntityManagerInterface $entityManager Entity Manager for persisting data
     *
     * @return Response HTTP response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/create', name: 'user_create', methods: 'GET|POST')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * Handles editing an existing user, including password changes.
     *
     * @param Request $request HTTP Request object
     * @param User    $user    User entity to be edited
     *
     * @return Response HTTP response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '\d+'], methods: 'GET|POST')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Delete action.
     *
     * Handles the deletion of a user.
     *
     * @param Request $request HTTP Request object
     * @param User    $user    User entity to be deleted
     *
     * @return Response HTTP response object
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'user_delete', requirements: ['id' => '\d+'], methods: 'POST')]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userService->delete($user);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));
        }

        return $this->redirectToRoute('user_index');
    }
}
