<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use App\Form\AccountType;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Bundle\SecurityBundle\Security;
use function Symfony\Component\Mime\toString;


#[Route('/account')]
class AccountController extends AbstractController
{


    #[Route('/', name: 'app_account_index', methods: ['GET'])]
    public function index(AccountRepository $accountRepository, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
       // $user=$doctrine->getRepository(User::class)->find($id);
        $accounts=$user->getAccountss();
        //return new Response('welcome '.$accounts->getName());
      //  $users=$doctrine->getRepository('App:User')->findAll();
        if($accounts!==null){
            return $this->render('account/index.html.twig', ['accounts'=>$accounts,
        ]);}
        return $this->redirectToRoute('app_account_new', [], Response::HTTP_SEE_OTHER);
   }

    #[Route('/new', name: 'app_account_new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine,Request $request, AccountRepository $accountRepository, SluggerInterface $slugger): Response
    {

        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {

            $image=$form->get('image')->getData();
            if($image){
                $originalFilename=pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename= $slugger ->slug($originalFilename);
                $newFilename=$safeFilename . '-' . uniqid() . '.' . $image ->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('accountImages_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );
                }
                $account->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );
            }

            $user = $this->getUser();
            $form=$user->getAccountss();

            $em = $doctrine->getManager();
            $em ->persist($account);
            $em ->flush();

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/new.html.twig', [
            'account' => $account,
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(ManagerRegistry $doctrine, int $id ,Request $request, Account $account, AccountRepository $accountRepository, SluggerInterface $slugger): Response
    {
        $em = $doctrine->getManager();
        $account = $em ->getRepository('App\Entity\Account')->find($id);

        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image=$form->get('image')->getData();
            if($image){
                $originalFilename=pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename= $slugger ->slug($originalFilename);
                $newFilename=$safeFilename . '-' . uniqid() . '.' . $image ->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('accountImages_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );
                }
                $account->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );
            }

            $em = $doctrine->getManager();
            $em ->persist($account);
            $em ->flush();

            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }
}
