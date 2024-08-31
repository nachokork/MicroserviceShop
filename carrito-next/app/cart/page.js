// app/cart/page.js
'use client';

import styles from './cart.module.css';
import { useState, useEffect } from 'react';

export default function Cart() {
    const [cartItems, setCartItems] = useState([]);
    const [products, setProducts] = useState([]);

    useEffect(() => {
        // Fetch cart items & product details
        fetch('http://localhost:58000/api/cart')
            .then(res => res.json())
            .then(data => setCartItems(data));

        const fetchProducts = async () => {
            const productData = await Promise.all(
                cartItems.map(item => fetch(`http://localhost:58000/api/products/${item.productId}`))
            );
            const products = await Promise.all(productData.map(res => res.json()));
            setProducts(products);
        };
        fetchProducts();
    }, [cartItems]);

    const handleQuantityChange = (productId, newQuantity) => {
        // Update the quantity in the cart
        setCartItems(prevCartItems =>
            prevCartItems.map(item => (item.productId === productId ? { ...item, quantity: newQuantity } : item))
        );

        // Update the cart on the server (optional)
        fetch(`http://localhost:58000/api/cart/${productId}`, {
            method: 'PATCH',
            body: JSON.stringify({ quantity: newQuantity }),
        });
    };

    const handlePlaceOrder = async () => {
        // Send a request to your API to place the order
        const response = await fetch('http://localhost:58000/api/orders', {
            method: 'POST',
            body: JSON.stringify({ cartItems }),
        });

        if (response.ok) {
            // Handle successful order placement (e.g., clear cart, redirect to confirmation page)
            setCartItems([]);
            console.log('Order placed successfully!');
        } else {
            // Handle order placement error
            console.error('Failed to place order!');
        }
    };

    return (
        <div className={styles.cartContainer}>
            <h1>Mi Carrito</h1>
            <ul className={styles.cartList}>
                {products.map(product => (
                    <li key={product.id} className={styles.cartItem}>
                        <div>
                            {product.name}
                            <img src={product.image} alt={product.name} />
                        </div>
                        <div>
                            Precio: ${product.price}
                            <div>
                                Cantidad:
                                <span>{cartItems.find(item => item.productId === product.id)?.quantity || 0}</span>
                                <div className={styles.quantityContainer}>
                                    <div className={styles.buttonContainer}>
                                        <button className={styles.Buttons}
                                            onClick={() => handleQuantityChange(product.id, product.quantity + 1)}>+
                                        </button>
                                        <button className={styles.Buttons}
                                            onClick={() => handleQuantityChange(product.id, Math.max(product.quantity - 1, 0))}>-
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                ))}
            </ul>
            <div className={styles.cartTotal}>
                Total: ${products.reduce((total, product) => total + product.price * product.quantity, 0)}
            </div>
            <button className={styles.placeOrderButton} onClick={handlePlaceOrder}>Realizar Pedido</button>
        </div>
    );
}