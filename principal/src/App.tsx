import {useEffect, useState} from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'
import axios from "axios";
import Producto from "./Producto.tsx";


function App() {
    const [productos, setProductos] = useState([]);

    useEffect(() => {
        const fetchProductos = async () => {
            try {
                const response = await axios.get('http://localhost:58000/api/products');

                setProductos(response.data);
                console.log(response.data);
            } catch (error) {
                console.error(error);
            }
        };
        fetchProductos();
    }, []);

    const handleAddToCart = (productId) => {
        console.log('Producto añadido al carrito:', productId);
        axios.post('http://localhost:3001/api/add/cart', { productId })
            .then(response => {
                console.log('Producto añadido al carrito:', response.data);
            })
            .catch(error => {
                console.error('Error al añadir al carrito:', error);
            });
    };

    return (
        <div>
            <h1>Productos</h1>
            <div className="product-list">
                {productos.map((producto) => (
                    <Producto key={producto.id} producto={producto} handleAddToCart={handleAddToCart} />
                ))}
            </div>
        </div>
    );
}

export default App
