<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function index(PinRepository $repo): Response
    {
        $pins = $repo->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_USER') and user.isVerified()")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        // if(! $this->getUser()){
        //     throw $this->createAccessDeniedException('Please, log in first !');
        // }

        // if(! $this->getUser()->isVerified()){
        //     throw $this->createAccessDeniedException('Your account is not veriffied !');
        // }
        
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created');

            return $this->redirectToRoute('app_pins_show', ['id' => $pin->getId()]);
        }
        return $this->render('pins/create.html.twig', [
            'monFormulaire' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/{id<\d+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
        // $pin = $repo->find($id);
        // if (!$pin) {
        //     throw $this->createNotFoundException('Pin N°' . $id . ' not found');
        // }
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/edit/{id<\d+>}", name="app_pins_edit", methods={"GET", "PUT"})
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('PIN_MANAGE', $pin);
        
        // if(! $this->getUser()){
        //     $this->addFlash('error', 'Please, log in first !');
        //     return $this->redirectToRoute('app_login');
        // }

        // if(! $this->getUser()->isVerified()){
        //     $this->addFlash('error', 'Your account is not veriffied !');
        //     return $this->redirectToRoute('app_home');
        // }
        
        // if($pin->getUser() != $this->getUser()){
        //     $this->addFlash('error', 'This pin does not belong to you !');
        //     return $this->redirectToRoute('app_home');
        // }
        
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Pin successfully updated');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'monFormulaire' => $form->createView()
        ]);
    }

    /**
     * @Route("/pins/delete/{id<\d+>}", name="app_pins_delete", methods={"DELETE"})
     * @IsGranted("PIN_MANAGE", subject="pin")
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        // if(! $this->getUser()){
        //     $this->addFlash('error', 'Please, log in first !');
        //     return $this->redirectToRoute('app_login');
        // }

        // if(! $this->getUser()->isVerified()){
        //     $this->addFlash('error', 'Your account is not veriffied !');
        //     return $this->redirectToRoute('app_home');
        // }
        
        // if($pin->getUser() != $this->getUser()){
        //     $this->addFlash('error', 'This pin does not belong to you !');
        //     return $this->redirectToRoute('app_home');
        // }
        
        if ($this->isCsrfTokenValid('pin_deletion_' . $pin->getId(), $request->request->get('csrf_token'))) {
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'Pin successfully deleted');
        }
        return $this->redirectToRoute('app_home');
    }
}
