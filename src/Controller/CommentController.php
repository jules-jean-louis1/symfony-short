<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Post;
use App\Form\Type\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    public function add(Request $request, Post $post, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentData = $form->getData();
            $user = $session->get('user');

            $comment = new Comment();
            $comment->setUser($user)
                ->setContent($commentData->getContent())
                ->setPost($post)
                ->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->render('comment/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
