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

    public function edit(Request $request, SessionInterface $session): Response
    {
        $data = $session->get('user');
        $userId = $data->getId();

        if (!$userId) {
            return new Response('Utilisateur non trouvé', Response::HTTP_FORBIDDEN);
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return new Response('Utilisateur non trouvé', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Mise à jour de la session
            $session->set('user', $user);
        }

        return $this->render('user/_edit_form.html.twig', [
            'EditForm' => $form->createView(),
        ]);
    }
}
