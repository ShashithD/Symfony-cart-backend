<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class ProductController extends AbstractController
{
    private $em;
    private $productRepository;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $em)
    {
        $this->productRepository = $productRepository;
        $this->em = $em;
    }

    //check, Is the first parameter a string and less than 255 characters. Second parameter is shown in the error message.
    public function checkString($parameter, $parameter_name) 
    {
        if (!isset($parameter)) {
            throw new Exception("Product " . $parameter_name . " is missing!");
        } elseif (!is_string($parameter)) {
            throw new Exception("Product " . $parameter_name . " must be a string!");
        } elseif (strlen($parameter) > 255) {
            throw new Exception(
                "Product " . $parameter_name . " must be shorter than 255 characters!"
            );
        } else {
            return true;
        }
    }

    //check, Is the first parameter an integer. Second parameter is shown in the error message.
    public function checkInteger($parameter, $parameter_name) 
    {
        if (!isset($parameter)) {
            throw new Exception("Product " . $parameter_name . " is missing!");
        } elseif (!is_int($parameter)) {
            throw new Exception("Product " . $parameter_name . " must be an integer!");
        } else {
            return true;
        }
    }

    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->json([
            'controller_name' => 'ProductController',
        ]);
    }

    //create a new product, post product properties in a JSON array.
    #[Route('/product/create', methods: ['POST'], name: 'create_product')]
    public function create(Request $request): Response
    {
        $product   = new Product();
        $parameter = json_decode($request->getContent(), true);

        try {
            $this->checkString($parameter['name'], "name");
            $this->checkInteger($parameter['price'], "price");
            $this->checkString($parameter['description'], "description");
            $this->checkInteger($parameter['qty'], "quantity");
            $this->checkString($parameter['image'], "image path");

            $product->setName($parameter['name']);
            $product->setPrice($parameter['price']);
            $product->setDescription($parameter['description']);
            $product->setQty($parameter['qty']);
            $product->setImage($parameter['image']);
        } catch(Exception $e) {
            $message = 'Message: ' . $e->getMessage();

            return $this->json($message);
        }

        $this->em->persist($product);
        $this->em->flush();

        return $this->json('Product created successfully!');
    }

    //upadate the product for given id, post product properties in a JSON array.
    #[Route('/product/update/{id}', methods: ['PUT'], name: 'update_product')]
    public function update(Request $request, $id): Response
    {
        $product    = $this->productRepository->find($id);
        $parameter  = json_decode($request->getContent(), true);

        try {
            $this->checkString($parameter['name'], "name");
            $this->checkInteger($parameter['price'], "price");
            $this->checkString($parameter['description'], "description");
            $this->checkInteger($parameter['qty'], "quantity");
            $this->checkString($parameter['image'], "image path");

            $product->setName($parameter['name']);
            $product->setPrice($parameter['price']);
            $product->setDescription($parameter['description']);
            $product->setQty($parameter['qty']);
        } catch(Exception $e) {
            $message = 'Message: ' . $e->getMessage();

            return $this->json($message);
        }

        $this->em->persist($product);
        $this->em->flush();

        return $this->json('Product updated successfully!');
    }

    //delate one product for given id.
    #[Route('/product/delete/{id}', methods: ['DELETE'], name: 'delaete_product')]
    public function delete(Request $request, $id): Response
    {
        $product = $this->productRepository->find($id);

        $this->em->remove($product);
        $this->em->flush();

        return $this->json('Product deleted successfully!');
    }

    //fetch all products in a JSON array.
    #[Route('/product/fetch-all', methods: ['GET'], name: 'fetch_all_product')]
    public function fetchAll(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->json($products);
    }

    //fetch one products for given id.
    #[Route('/product/fetch-one/{id}', methods: ['GET'], name: 'fetch_one_product')]
    public function fetchOne($id): Response
    {
        $product = $this->productRepository->find($id);

        return $this->json($product);   
    }
}
