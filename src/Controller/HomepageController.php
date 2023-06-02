<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController {
    #[Route('/', name: 'home')]
    public function home(): Response {
        return $this->render('home/homepage.html.twig', []);
    }
}