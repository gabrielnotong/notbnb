<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnounceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_list")
     * @param AdRepository $adRepository
     * @return Response
     */
    public function index(AdRepository $adRepository)
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ads/new", name="ads_create")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $ad = new Ad();
        $form = $this->createForm(AnnounceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "Ad <strong>{$ad->getTitle()}</strong> successfully added !");

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug(),]);
        }

        return $this->render('ad/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ads/{slug}", name="ads_show")
     * @param Ad $ad
     * @return Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    /**
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AnnounceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success', "Ad <strong>{$ad->getTitle()}</strong> successfully updated !");

            return $this->redirectToRoute('ads_show', ['slug' => $ad->getSlug(),]);
        }

        return $this->render('ad/edit.html.twig', ['form' => $form->createView(), 'ad' => $ad]);
    }
}
