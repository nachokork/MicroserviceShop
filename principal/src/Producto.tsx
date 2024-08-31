import './Producto.css'; // Importar el archivo CSS para los estilos

function Producto({ producto ,handleAddToCart }) {
    return (
        <div className="product-card">
            <img src={producto.image} alt={producto.name} />
            <h2>{producto.name}</h2>
            <p>{producto.description}</p>
            <div className="product-details">
                <p>Precio: ${producto.price}</p>
                <p>Stock: {producto.stock}</p>
                <button onClick={() => handleAddToCart(producto.id)}>Añadir al carrito</button>
            </div>
        </div>
    );
}

export default Producto;