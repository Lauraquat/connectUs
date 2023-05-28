<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Like;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use App\Repository\LikeRepository;
use App\Repository\RecruterRepository;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/candidate')]
class CandidateController extends AbstractController
{
    #[Route('/', name: 'app_candidate_index', methods: ['GET'])]
    public function index(CandidateRepository $candidateRepository): Response
    {
        return $this->render('candidate/index.html.twig', [
            'candidates' => $candidateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_candidate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CandidateRepository $candidateRepository, SluggerInterface $slugger, TranslatorInterface $translator): Response
    {
        $candidate = new Candidate();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $candidate->setOwner($this->getUser());

            /** @var UploadedFile $CV */
            $CV = $form->get('CV')->getData();

            if ($CV) {
                $originalFilename = pathinfo($CV->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$CV->guessExtension();

                try {
                    $CV->move(
                        $this->getParameter('CV_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new DomainException($translator->trans("Error during CV import"));
                }
                $candidate->setCV($newFilename);
            }

            $candidateRepository->save($candidate, true);

            return $this->redirectToRoute('app_recruter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/new.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidate_show', methods: ['GET'])]
    public function show(Candidate $candidate): Response
    {
        return $this->render('candidate/show.html.twig', [
            'candidate' => $candidate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_candidate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidate $candidate, CandidateRepository $candidateRepository): Response
    {
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $candidateRepository->save($candidate, true);

            return $this->redirectToRoute('app_recruter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_candidate_delete', methods: ['POST'])]
    public function delete(Request $request, Candidate $candidate, CandidateRepository $candidateRepository, SessionInterface $session): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidate->getId(), $request->request->get('_token'))) {
            $session = new Session();
            $session->invalidate();
            $candidateRepository->remove($candidate, true);
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{candidateID}/{recruterID}', name: 'app_candidate_like', methods: ['GET'])]
    public function likeCandidate(Request $request,LikeRepository $likeRepository,CandidateRepository $candidateRepository,RecruterRepository $recruterRepository): Response
    {
        $candidateId = $request->get('candidateID');
        $recruterId = $request->get('recruterID');

        
        $candidate = $candidateRepository->find(intval($candidateId));
        $recruter = $recruterRepository->findOneBy(['owner' => $recruterId]);
        //$likeRepository->save($likeRepository,true);
        

        // Vérifier si les instances sont valides
        if (!$candidate ) {
            return new Response('Candidat introuvable.', Response::HTTP_NOT_FOUND);
        }elseif(!$recruter){
            return new Response('Recruteur introuvable.', Response::HTTP_NOT_FOUND);
        }

        $like = new Like();
        $like->setCandidate($candidate);
        $like->setRecruter($recruter);
        $like->setDate(new \DateTime());

        $likeRepository->save($like, true);
        //return new Response('Like enregistré avec succès !');
        
        return $this->redirectToRoute('app_candidate_show', ['id' => $candidateId], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/candidate-like', name: 'app_candidate_like', methods: ['GET'])]
    public function showCandidateLike(CandidateRepository $candidateRepository): Response
    {
        
        $likes = $candidateRepository->likeCandidate();

        return $this->render('candidate/candidate.html.twig', [
            'likes' => $likes,
        ]);
    }
    
}
