<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\UserLoginType;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->entityManager = $entityManager;;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(UserLoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $credentials = $form->getData();
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($credentials->getEmail());

            if ($user && password_verify($credentials->getPassword(), $user->getPassword())) {
                $csrfToken = $this->csrfTokenManager->getToken('authenticate')->getValue();

                $session->set('csrf_token', $csrfToken);
                $session->set('user', $user);
                $this->addFlash('success', 'Vous êtes connecté.');

                return $this->redirectToRoute('app_home');

            } else {
                $this->addFlash('danger', 'Identifiants incorrects.');
            }
        }

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(UserRegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_ARGON2I));
            $user->setCreatedAt(new \DateTimeImmutable());
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}