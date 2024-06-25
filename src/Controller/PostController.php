<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\Type\PostAddType;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $entityManager, PostRepository $postRepository)
    {
        $this->entityManager = $entityManager;
        $this->postRepository = $postRepository;
    }

    #[Route('/post/add', name: 'app_post_add')]
    public function add(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(PostAddType::class);
        $form->handleRequest($request);
        $userData = $session->get('user') ?? false;

        if (!$userData) {
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $user = $this->entityManager->getRepository(User::class)->find($userData->getId());

            $post->setOwner($user);
            $post->setCreatedAt(new DateTimeImmutable());

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            $this->addFlash('success', 'Post ajouté avec succès.');
            return $this->redirectToRoute('app_posts');
        }
        return $this->render('post/add.html.twig', [
            'formAddPost' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/edit', name: 'app_post_edit')]
    public function edit(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(PostAddType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_posts');
        }
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'formEdit' => $form
        ]);
    }

    #[Route('/post/{id}', name: 'app_show_post')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/posts', name: 'app_posts')]
    public function getAll(): Response
    {
        $posts = $this->postRepository->findAll();
        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

}
