<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/profile', name: 'app_profile')]
    public function edit(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $user = $session->get('user');
        $userData = $this->entityManager->getRepository(User::class)->find($user->getId());

        $form = $this->createForm(UserType::class, $userData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $edit = $form->getData();
            $entityManager->persist($edit);
            $entityManager->flush();
        }

        return $this->render('user/index.html.twig', [
            'EditForm' => $form->createView(),
        ]);
    }
}
