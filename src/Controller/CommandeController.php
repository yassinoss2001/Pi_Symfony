<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\LivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/ExportPdf', name: 'app_commande_export', methods: ['GET', 'POST'])]
    public function ExportPdf(EntityManagerInterface $entityManager) :Response
    {
        $commandes = $entityManager
        ->getRepository(Commande::class)
        ->findAll();
          $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $html = $this->renderView('commande/pdf.html.twig', [
           
            'commandes' => $commandes,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    #[Route('/', name: 'app_commande_index', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $entityManager,LivraisonRepository $repo,CommandeRepository $commandeRepository,Request $request): Response
    {

        $search=$request->request->get('search');
        if($search)
        {
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('c')
                     ->from(Commande::class, 'c')
                     ->where($queryBuilder->expr()->like('c.adresse', ':search'))
                     ->setParameter('search', '%' . $search . '%');
        
        $commandes = $queryBuilder->getQuery()->getResult(); 
        }else
        $commandes=$commandeRepository->findAll();

        return $this->render('commande/index.html.twig', [
            'livraisons' => $repo->findAll(),
            'commandes' => $commandes,
            'Cash' => $commandeRepository->ModeP("Cash"),
            'Carte' => $commandeRepository->ModeP("Carte Bancaire"),
        ]);
    }
    #[Route('/front', name: 'app_commande_index_front', methods: ['GET','POST'])]
    public function indexFront(Request $request,PaginatorInterface $paginator,EntityManagerInterface $entityManager,CommandeRepository $commandeRepository): Response
    {
        
  
   $queryBuilder = $entityManager->getRepository(Commande::class)->createQueryBuilder('c');
   $dateFilter = $request->request->get('date_filter');
if ($dateFilter) {
    $startDate = new \DateTime($dateFilter);
    $endDate = (clone $startDate)->modify('+1 day'); 
    $queryBuilder
        ->andWhere('c.dateCommande >= :start_date')
        ->andWhere('c.dateCommande < :end_date')
        ->setParameter('start_date', $startDate->format('Y-m-d'))
        ->setParameter('end_date', $endDate->format('Y-m-d'));
}

   
   $pagination = $paginator->paginate(
       $queryBuilder->getQuery(),
       $request->query->getInt('page', 1),
       3
   );
        return $this->render('commande/indexFront.html.twig', [
            'commandes' => $pagination,
        ]);
    }
    #[Route('/livreur', name: 'app_commande_index_livreur', methods: ['GET'])]
    public function indexLivreur(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/indexLivreur.html.twig', [
            'commandes' => $commandeRepository->CommandesPrets("Pret"),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setIdClient(1);
            $commande->setDateCommande(new \DateTime());
            $commande->setEtatCommande("En Preparation");
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
    #[Route('/accept/{id}', name: 'app_commande_accept', methods: ['GET'])]
    public function Accept(LivraisonRepository $repo,Commande $commande, EntityManagerInterface $entityManager,CommandeRepository $commandeRepository): Response
    {
        $commande->setEtatCommande("Pret");
        $entityManager->flush();

        return $this->render('commande/index.html.twig', [
            'livraisons' => $repo->findAll(),
            'commandes' => $commandeRepository->findAll(),
            'Cash' => $commandeRepository->ModeP("Cash"),
            'Carte' => $commandeRepository->ModeP("Carte Bancaire"),
        ]);
    }
    #[Route('/decline/{id}', name: 'app_commande_decline', methods: ['GET'])]
    public function Decline(LivraisonRepository $repo,Commande $commande, EntityManagerInterface $entityManager,CommandeRepository $commandeRepository): Response
    {
        $commande->setEtatCommande("Annulee");
        $entityManager->flush();

        return $this->render('commande/index.html.twig', [
            'livraisons' => $repo->findAll(),
            'commandes' => $commandeRepository->findAll(),
            'Cash' => $commandeRepository->ModeP("Cash"),
            'Carte' => $commandeRepository->ModeP("Carte Bancaire"),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

   
}
