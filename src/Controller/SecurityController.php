<?php
namespace App\Controller;

use App\Form\Type\UserRegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\UserLoginType;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        $form = $this->createForm(UserLoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO
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
        // Todo: Enregistrer l'utilisateur
    }
    
    return $this->render('security/register.html.twig', [
        'registerForm' => $form->createView(),
    ]);
}
}