<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Like;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use App\Repository\LikeRepository;
use App\Repository\RecruterRepository;
use DomainException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    #[IsGranted('ROLE_RECRUTER')]
    #[Route('/', name: 'app_candidate_index', methods: ['GET'])]
    public function index(CandidateRepository $candidateRepository, RecruterRepository $recruterRepository, LikeRepository $likeRepository ): Response
    {
        $recrutersWhoLikedCandidate = [];
        if($recruter = $recruterRepository->findOneByOwner($this->getUser())){
            $recrutersWhoLikedCandidate = $likeRepository->recrutersWhoLikedCandidate($recruter->getId());
        }

        return $this->render('candidate/index.html.twig', [
            'candidates' => $candidateRepository->findAll(),
            'recrutersWhoLikedCandidate' => $recrutersWhoLikedCandidate,
        ]);
    }

    #[IsGranted('ROLE_CANDIDATE')]
    #[Route('/new', name: 'app_candidate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CandidateRepository $candidateRepository, SluggerInterface $slugger, TranslatorInterface $translator): Response
    {
        $candidate = new Candidate();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $candidate->setOwner($this->getUser());

                /** @var UploadedFile $CV */
                $CV = $form->get('CV')->getData();

                if ($CV) {
                    $originalFilename = pathinfo($CV->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $CV->guessExtension();

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

                // Utilisation du TranslatorInterface (en paramètre de la fonction) pour effectuer les traductions (stockées dans translations/messages.fr.yaml)
                $this->addFlash('success', $translator->trans('The profil has been created successfully.'));

                return $this->redirectToRoute('app_recruter_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('danger', $translator->trans('Error during creation. Please retry.'));
            }
        }

        return $this->renderForm('candidate/new.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_RECRUTER')]
    #[Route('/{id}', name: 'app_candidate_show', methods: ['GET'])]
    public function show(Candidate $candidate, RecruterRepository $recruterRepository): Response
    {
        $recruter = $recruterRepository->findOneByOwner($this->getUser()->getId());

        return $this->render('candidate/show.html.twig', [
            'candidate' => $candidate,
            'recruter' => $recruter,
        ]);
    }

    #[IsGranted('ROLE_CANDIDATE')]
    #[Route('/{id}/edit', name: 'app_candidate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidate $candidate, CandidateRepository $candidateRepository, SluggerInterface $slugger, TranslatorInterface $translator): Response {
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                /** @var UploadedFile $CV */
                $CV = $form->get('CV')->getData();

                if ($CV) {
                    $originalFilename = pathinfo($CV->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $CV->guessExtension();

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

                // Utilisation du TranslatorInterface (en paramètre de la fonction) pour effectuer les traductions (stockées dans translations/messages.fr.yaml)
                $this->addFlash('success', $translator->trans('The profil has been modified successfully.'));

                return $this->redirectToRoute('app_recruter_index', [], Response::HTTP_SEE_OTHER);
            }else{
                $this->addFlash('danger', $translator->trans('Error during edition. Please retry'));
            }
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_CANDIDATE')]
    #[Route('/{id}', name: 'app_candidate_delete', methods: ['POST'])]
    public function delete(Request $request, Candidate $candidate, CandidateRepository $candidateRepository, SessionInterface $session, TranslatorInterface $translator, LikeRepository $likeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidate->getId(), $request->request->get('_token'))) {
            $session = new Session();
            $session->invalidate();
            $likeRepository->removeLikesBy(null, $candidate);

            $candidateRepository->remove($candidate, true);

            // Utilisation du TranslatorInterface (en paramètre de la fonction) pour effectuer les traductions (stockées dans translations/messages.fr.yaml)
            $this->addFlash('success', $translator->trans('The profil has been deleted successfully.'));

        }else {
            $this->addFlash('danger', $translator->trans('Invalid token.'));
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_CANDIDATE')]
    #[Route('/like/{id}', name: 'app_candidate_like', methods: ['GET'])]
    public function likeCandidate(Request $request, Candidate $candidate, LikeRepository $likeRepository, RecruterRepository $recruterRepository): Response
    {
        $recruter = $recruterRepository->findOneByOwner($this->getUser()->getId());

        $like = new Like();
        $like->setCandidate($candidate);
        $like->setRecruter($recruter);
        $like->setLikedType($this->getUser()->getType());

        $likeRepository->save($like, true);

        return $this->redirectToRoute('app_candidate_show', ['id' => $candidate->getId()], Response::HTTP_SEE_OTHER);
    }
}
