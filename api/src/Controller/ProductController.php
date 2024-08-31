<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    public function __construct(
        protected ProductRepository $productRepository,

    ){}

    #[Route('api/products', name: 'products', methods: ['GET'])]
    public function getProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        $data = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'stock' => $product->getStock(),
            ];
        }, $products);

        return $this->json($data);
    }

    #[Route('/api/products', name: 'add_product', methods: ['PUT'])]
    public function addProduct(Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);
        $product = new Product();
        $product->setName($requestContent['name']);
        $product->setDescription($requestContent['description']);
        $product->setPrice($requestContent['price']);
        $product->setStock($requestContent['stock']);
        return $this->productRepository->save($product);
    }

    #[Route('/api/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function removeProduct($id): JsonResponse
    {
        return $this->productRepository->deleteProductById($id);
    }

    #[Route('/api/products/{id}', name: 'delete_product', methods: ['GET'])]
    public function searchById($id)
    {
        $product = $this->productRepository->findOneById($id);
        if ($product) {
            return new JsonResponse($this->productRepository->toArray($product));
        } else {
            return new JsonResponse(['message' => 'Producto no encontrado'], 404);
        }
    }
}