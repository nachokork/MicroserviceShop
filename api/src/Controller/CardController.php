<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    public function __construct(
        protected CartItemRepository $cartItemRepository,
        protected ProductRepository $productRepository,
        protected EntityManagerInterface $entityManager
    ){}

    #[Route('api/cart', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request): JsonResponse
    { $data = json_decode($request->getContent(), true);

        $productId = $data['productId'];
        $quantity = $data['quantity'];

        $product = $this->productRepository->findOneById($productId);

        if (!$product) {
            return new JsonResponse(['status' => 'Producto no encontrado'], 404);
        }

        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['status' => 'Usuario no autenticado'], 401);
        }

        $existingCartItem = $this->cartItemRepository->findOneByProductAndUser($product,$user);

        if ($existingCartItem) {
            $existingCartItem->setCuantity($existingCartItem->getCuantity() + $quantity);
            $this->entityManager->persist($existingCartItem);
        } else {
            $cartItem = new CartItem();
            $cartItem->setProduct($product);
            $cartItem->setCuantity($quantity);
            $cartItem->setUser($this->getUser());

            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Producto añadido al carrito'], 201);
    }

    #[Route('api/cart', name: 'get_cards', methods: ['GET'])]

    public function getCartItems(CartItemRepository $cartItemRepository): JsonResponse
    {
        $cartItems = $cartItemRepository->findAll();

        $data = [];
        foreach ($cartItems as $item) {
            $data[] = [
                'id' => $item->getId(),
                'productId' => $item->getProduct()->getId(),
                'quantity' => $item->getCuantity(),
            ];
        }

        return new JsonResponse($data, 200);
    }

    #[Route('api/cart/{id}', name: 'card_delete', methods: ['DELETE'])]
    public function removeCartItem(int $id, CartItemRepository $cartItemRepository): JsonResponse
    {
        $cartItem = $cartItemRepository->find($id);

        if (!$cartItem) {
            return new JsonResponse(['status' => 'Elemento no encontrado'], 404);
        }

        $this->cartItemRepository->delete($cartItem);

        return new JsonResponse(['status' => 'Elemento eliminado del carrito'], 200);
    }

}