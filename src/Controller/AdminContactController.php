<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admincontact')]
class AdminContactController extends AbstractController
{
    #[Route('/', name: 'app_admincontact_index', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('admin_contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }
}
