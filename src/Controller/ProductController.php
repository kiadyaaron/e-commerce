<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Classe\Search;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/products', name: 'products')]
    public function index(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

      
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig',[
            'products'=> $products,
            'form' => $form->createView()
        ]);
    }


    #[Route('/product/{slug}', name: 'product')]
    public function show($slug)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        $products = $this->entityManager->getRepository(Product::class)->findBy(['isBest' => true]);
        if (!$product) {
            return $this->redirectToRoute('products');
        }
        return $this->render('product/show.html.twig',[
            'product'=> $product,
            'products' => $products
        ]);
    }
}
