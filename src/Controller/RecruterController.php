<?php

namespace App\Controller;

use App\Entity\Recruter;
use App\Entity\User;
use App\Form\RecruterType;
use App\Repository\RecruterRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recruter')]
class RecruterController extends AbstractController
{
    #[Route('/', name: 'app_recruter_index', methods: ['GET'])]
    public function index(RecruterRepository $recruterRepository): Response
    {
        return $this->render('recruter/index.html.twig', [
            'recruters' => $recruterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recruter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RecruterRepository $recruterRepository): Response
    {
        $recruter = new Recruter();
        $form = $this->createForm(RecruterType::class, $recruter);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $recruter->setOwner($user);
            $recruterRepository->save($recruter, true);

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recruter/new.html.twig', [
            'recruter' => $recruter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recruter_show', methods: ['GET'])]
    public function show(Recruter $recruter): Response
    {
        return $this->render('recruter/show.html.twig', [
            'recruter' => $recruter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recruter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recruter $recruter, RecruterRepository $recruterRepository): Response
    {
        $form = $this->createForm(RecruterType::class, $recruter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recruterRepository->save($recruter, true);

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recruter/edit.html.twig', [
            'recruter' => $recruter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recruter_delete', methods: ['POST'])]
    public function delete(Request $request, Recruter $recruter, RecruterRepository $recruterRepository, SessionInterface $session): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recruter->getId(), $request->request->get('_token'))) {
            $session = new Session();
            $session->invalidate();
            $recruterRepository->remove($recruter, true);
        }

        return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
    }
}
