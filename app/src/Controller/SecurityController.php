<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Service\RegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SecurityController.
 *
 * Controller responsible for handling security-related actions, such as login,
 * logout, registration, and access denied actions.
 */
class SecurityController extends AbstractController
{
    /**
     * Login action.
     *
     * @param AuthenticationUtils $authenticationUtils Provides utilities for retrieving login errors and last username
     *
     * @return Response HTTP response object for login page
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Logout action.
     *
     * This action is intercepted by the firewall and never executed.
     * Throws a logic exception if called directly.
     *
     * @throws \LogicException
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Access denied action.
     *
     * Displays access denied page when a user does not have appropriate permissions.
     *
     * @return Response HTTP response object for access denied page
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/access-denied', name: 'app_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/access_denied.html.twig');
    }

    /**
     * Registration action.
     *
     * Handles user registration by displaying a form and processing the submitted data.
     * Redirects to login page on successful registration.
     *
     * @param Request                      $request             HTTP request object
     * @param RegistrationServiceInterface $registrationService Registration service to handle user registration
     * @param TranslatorInterface          $translator          Translator service to provide localized messages
     *
     * @return Response HTTP response object for registration page
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/register', name: 'app_register')]
    public function register(Request $request, RegistrationServiceInterface $registrationService, TranslatorInterface $translator): Response
    {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return $this->redirectToRoute('wallet');
        }

        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationService->registerUser($user);

            $this->addFlash('success', $translator->trans('message.registration_success'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
