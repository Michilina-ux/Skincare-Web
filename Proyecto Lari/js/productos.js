
        // Base de datos de productos
        const products = [
            {
                id: 1,
                name: "Gel Limpiador Suave",
                category: "limpieza",
                price: 299,
                description: "Limpieza profunda sin resecar tu piel",
                image: "imagenes/pro_9.png" // Cambia por tu URL
            },
            {
                id: 2,
                name: "Serum Vitamina C",
                category: "serums",
                price: 599,
                description: "Ilumina y protege tu piel del daño ambiental",
                image: "imagenes/pro_2.png" // Cambia por tu URL
            },
            {
                id: 3,
                name: "Crema Hidratante",
                category: "hidratacion",
                price: 399,
                description: "Hidratación profunda de 24 horas",
                image: "imagenes/pro_5.png" // Cambia por tu URL
            },
            {
                id: 4,
                name: "Serum Retinol",
                category: "anti-aging",
                price: 799,
                description: "Reduce arrugas y líneas de expresión",
                image: "imagenes/pro_1.png" // Cambia por tu URL
            },
            {
                id: 5,
                name: "Mascarilla Hidratante",
                category: "mascarillas",
                price: 199,
                description: "Hidratación instantánea en 15 minutos",
                image: "imagenes/pro_4.png" // Cambia por tu URL
            },
            {
                id: 6,
                name: "Tónico Facial",
                category: "limpieza",
                price: 349,
                description: "Equilibra el pH de tu piel",
                image: "imagenes/pro_3.png" // Cambia por tu URL
            },
            {
                id: 7,
                name: "Serum Ácido Hialurónico",
                category: "serums",
                price: 699,
                description: "Hidratación profunda y efecto plumping",
                image: "imagenes/pro_7.png" // Cambia por tu URL
            },
            {
                id: 8,
                name: "Crema Anti-Edad",
                category: "anti-aging",
                price: 899,
                description: "Fórmula avanzada con péptidos",
                image: "imagenes/pro_8.png" // Cambia por tu URL
            }
        ];

        // Carrito de compras
        let cart = [];
        let currentCategory = 'all';

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            renderProducts();
            setupCategoryFilters();
            updateCartCount();
        });

        // Renderizar productos
        function renderProducts(category = 'all') {
            const productsGrid = document.getElementById('products-grid');
            const filteredProducts = category === 'all' ? products : products.filter(p => p.category === category);

            productsGrid.innerHTML = filteredProducts.map(product => `
                <div class="product-card">
                    <div class="product-image">
                        <img src="${product.image}" alt="${product.name}" onerror="this.style.display='none'">
                    </div>
                    <div class="product-info">
                        <div class="product-category">${getCategoryName(product.category)}</div>
                        <h3 class="product-name">${product.name}</h3>
                        <p class="product-description">${product.description}</p>
                        <div class="product-price">$${product.price}</div>
                        <div class="product-actions">
                            <button class="btn btn-primary" onclick="addToCart(${product.id})">
                                Agregar al Carrito
                            </button>
                            <button class="btn btn-secondary" onclick="buyNow(${product.id})">
                                Comprar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Configurar filtros de categorías
        function setupCategoryFilters() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remover clase active de todos los botones
                    filterButtons.forEach(b => b.classList.remove('active'));
                    // Agregar clase active al botón clickeado
                    btn.classList.add('active');
                    
                    const category = btn.dataset.category;
                    currentCategory = category;
                    renderProducts(category);
                });
            });
        }

        // Obtener nombre de categoría
        function getCategoryName(category) {
            const categoryNames = {
                'limpieza': 'Limpieza',
                'hidratacion': 'Hidratación',
                'anti-aging': 'Anti-Aging',
                'serums': 'Serums',
                'mascarillas': 'Mascarillas'
            };
            return categoryNames[category] || category;
        }

        // Agregar al carrito
        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ ...product, quantity: 1 });
            }

            updateCartCount();
            renderCart();
            showNotification('Producto agregado al carrito ✅');
        }

        // Comprar ahora
        function buyNow(productId) {
            addToCart(productId);
            toggleCart();
        }

        // Actualizar contador del carrito
        function updateCartCount() {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cart-count').textContent = count;
        }

        // Renderizar carrito
        function renderCart() {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');

            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <p>Tu carrito está vacío</p>
                        <p>¡Agrega algunos productos increíbles!</p>
                    </div>
                `;
                cartTotal.style.display = 'none';
            } else {
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="${item.image}" alt="${item.name}" onerror="this.textContent='📦'">
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">$${item.price}</div>
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="changeQuantity(${item.id}, -1)">-</button>
                                <span>${item.quantity}</span>
                                <button class="quantity-btn" onclick="changeQuantity(${item.id}, 1)">+</button>
                                <button class="quantity-btn" onclick="removeFromCart(${item.id})" style="background: #ff4757; margin-left: 10px;">🗑</button>
                            </div>
                        </div>
                    </div>
                `).join('');

                const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                document.getElementById('total-amount').textContent = total;
                cartTotal.style.display = 'block';
            }
        }

        // Cambiar cantidad
        function changeQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCartCount();
                    renderCart();
                }
            }
        }

        // Remover del carrito
        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartCount();
            renderCart();
            showNotification('Producto eliminado del carrito 🗑');
        }

        // Toggle carrito
        function toggleCart() {
            const cartSidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('overlay');
            
            cartSidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            
            if (cartSidebar.classList.contains('open')) {
                renderCart();
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }

        // Checkout
        function checkout() {
            if (cart.length === 0) {
                showNotification('Tu carrito está vacío 🛒');
                return;
            }

            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
            
            alert(`¡Gracias por tu compra! 🎉\n\nProductos: ${itemCount}\nTotal: $${total}\n\nSerás redirigido al proceso de pago...`);
            
            // Aquí integrarías con tu pasarela de pago
            // Por ejemplo: Stripe, PayPal, MercadoPago, etc.
            
            // Limpiar carrito después de la compra
            cart = [];
            updateCartCount();
            renderCart();
            toggleCart();
        }

        // Mostrar notificación
        function showNotification(message) {
            // Crear elemento de notificación
            const notification = document.createElement('div');
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: #4CAF50;
                color: white;
                padding: 1rem 2rem;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                z-index: 3000;
                animation: slideInRight 0.3s ease;
            `;

            document.body.appendChild(notification);

            // Remover después de 3 segundos
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Agregar estilos de animación para notificaciones
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
