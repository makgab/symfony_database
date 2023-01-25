<?php
// src/Controller/ProductController.php
namespace App\Controller;

// ...
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Type\ProductType;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'create_product')]
    public function createProduct(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(1999);

        # FORM ------------------------------------------------
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $product = $form->getData();

            // ... perform some action, such as saving the task to the database
            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($product);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            return $this->redirectToRoute('create_product');
        }

        return $this->render('product/new.html.twig', [
        'form' => $form,
        ]);
        # -----------------------------------------------------
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        // return new Response('Check out this great product: '.$product->getName().' ('.$product->getId().') '.$product->getPrice());

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('product/show.html.twig', ['product' => $product]);
    }


    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        # FORM ------------------------------------------------
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $product = $form->getData();

            // ... perform some action, such as saving the task to the database
            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($product);
            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            return $this->redirectToRoute('product_show', ['id' => $id]);
        }

        return $this->render('product/new.html.twig', [
        'form' => $form,
        ]);
        # -----------------------------------------------------
    }


    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $pname = $product->getName();
        $entityManager->remove($product);
        $entityManager->flush();

        return new Response('Deleted product: '.$id. ', ' . $pname);
    }
}

