<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/project')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine,Request $request, ProjectRepository $projectRepository ,SluggerInterface $slugger): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image=$form->get('image')->getData();
            if($image){
                $originalFilename=pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename= $slugger ->slug($originalFilename);
                $newFilename=$safeFilename . '-' . uniqid() . '.' . $image ->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('projectImages_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );
                }
                $project->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );
            }

            $em = $doctrine->getManager();
            $em ->persist($project);
            $em ->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(ManagerRegistry $doctrine, int $id ,Request $request, Project $project, ProjectRepository $projectRepository ,SluggerInterface $slugger): Response
    {
        $em = $doctrine->getManager();
        $project = $em ->getRepository('App\Entity\Project')->find($id);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image=$form->get('image')->getData();
            if($image){
                $originalFilename=pathinfo($image->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFilename= $slugger ->slug($originalFilename);
                $newFilename=$safeFilename . '-' . uniqid() . '.' . $image ->guessExtension();

                try{
                    $image->move(
                        $this->getParameter('projectImages_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );
                }
                $project->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );
            }

            $em = $doctrine->getManager();
            $em ->persist($project);
            $em ->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);



    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
