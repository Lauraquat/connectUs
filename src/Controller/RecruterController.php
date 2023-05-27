<?php

namespace App\Controller;

use App\Entity\Recruter;
use App\Entity\User;
use App\Form\RecruterType;
use App\Repository\RecruterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    public function new(Request $request, RecruterRepository $recruterRepository, TranslatorInterface $translator): Response
    {
        $recruter = new Recruter();
        $form = $this->createForm(RecruterType::class, $recruter);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $recruter->setOwner($user);
            $recruterRepository->save($recruter, true);

            // Utilisation du TranslatorInterface (en paramètre de la fonction) pour effectuer les traductions (stockées dans translations/messages.fr.yaml)
            $this->addFlash('success', $translator->trans('The profil has been created successfully.'));

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }else {
            $this->addFlash('danger', $translator->trans('Error during creation. Please retry.'));
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
    public function edit(Request $request, Recruter $recruter, RecruterRepository $recruterRepository, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(RecruterType::class, $recruter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recruterRepository->save($recruter, true);

            // Utilisation du TranslatorInterface (en paramètre de la fonction) pour effectuer les traductions (stockées dans translations/messages.fr.yaml)
            $this->addFlash('success', $translator->trans('The profil has been modified successfully.'));

            return $this->redirectToRoute('app_candidate_index', [], Response::HTTP_SEE_OTHER);
        }else{
            $this->addFlash('danger', $translator->trans('Error during edition. Please retry'));
        }

        return $this->renderForm('recruter/edit.html.twig', [
            'recruter' => $recruter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recruter_delete', methods: ['POST'])]
    public function delete(Request $request, Recruter $recruter, RecruterRepository $recruterRepository, SessionInterface $session, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recruter->getId(), $request->request->get('_token'))) {
            $session = new Session();
            $session->invalidate();
            $recruterRepository->remove($recruter, true);

            $this->addFlash('success', $translator->trans('The profil has been deleted successfully.'));

        }else {
            $this->addFlash('danger', $translator->trans('Invalid token.'));
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
