<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserSettingsController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/settings')]
class UserSettingsController extends AbstractController
{
    /**
     * Change password action.
     *
     * @param Request                     $request        HTTP request
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param EntityManagerInterface      $entityManager  Entity manager
     * @param TranslatorInterface         $translator     Translator
     * @param TokenStorageInterface       $tokenStorage   Token storage
     * @param SessionInterface            $session        Session
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/change-password', name: 'user_settings', methods: 'GET|POST')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, TranslatorInterface $translator, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('message.password_changed_successfully'));

            $tokenStorage->setToken(null);
            $session->invalidate();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
