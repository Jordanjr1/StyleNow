<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'NewStyle';
$username = 'root';
$password = 'root';

// Conectar a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En producción, manejar el error adecuadamente
    $pdo = null;
}

// Simulación de datos (en producción estos vendrían de la base de datos)
$empleado_actual = [
    'id' => 1,
    'nombre' => 'Ana García',
    'sucursal_id' => 1,
    'sucursal_nombre' => 'Centro'
];

// Datos de ejemplo para mostrar la estructura
$productos_ejemplo = [
    ['prd_id' => 1, 'prd_nombre' => 'Tinte Rubio Dorado #8.3', 'prd_stockActual' => 24, 'prd_precioCompra' => 8.50, 'prd_unidadMedida' => 'unidad', 'categoria' => 'Tintes'],
    ['prd_id' => 2, 'prd_nombre' => 'Shampoo Anticaspa (1L)', 'prd_stockActual' => 7, 'prd_precioCompra' => 14.20, 'prd_unidadMedida' => 'litro', 'categoria' => 'Shampoo'],
    ['prd_id' => 3, 'prd_nombre' => 'Crema Hidratante Facial', 'prd_stockActual' => 14, 'prd_precioCompra' => 12.40, 'prd_unidadMedida' => 'unidad', 'categoria' => 'Facial'],
];

$metodos_pago = [
    'efectivo' => '💵 Efectivo',
    'tarjeta_credito' => '💳 Tarjeta Crédito',
    'tarjeta_debito' => '💳 Tarjeta Débito',
    'transferencia' => '🏦 Transferencia',
    'puntos' => '⭐ Puntos'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewStyle - Sistema de Ventas</title>
    <style>
        :root {
            --bg-primary: #f6f0e8;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #5d6d7e;
            --text-muted: #7f8c8d;
            --accent-primary: #e8b4bc;
            --accent-secondary: #d48a95;
            --border-color: #e0e0e0;
            --hover-bg: #f8f9fa;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--accent-primary);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        .sale-info {
            background: var(--bg-card);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            text-align: right;
        }

        .sale-number {
            font-size: 18px;
            font-weight: 600;
            color: var(--accent-primary);
        }

        .sale-date {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Main Layout */
        .main-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        @media (max-width: 1024px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Left Column */
        .left-column {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* Right Column */
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* Sections */
        .section {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 138, 149, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            opacity: 0.9;
        }

        /* Client Info */
        .client-info-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .client-selected {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(232, 180, 188, 0.1);
            border-radius: 8px;
            margin-bottom: 15px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
            padding: 5px;
        }

        .product-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: var(--accent-primary);
        }

        .product-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
            font-size: 14px;
        }

        .product-price {
            color: var(--accent-primary);
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 5px;
        }

        /* Cart Items */
        .cart-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .cart-item {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Summary */
        .summary-grid {
            display: grid;
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border-color);
        }

        .summary-row.total {
            border-top: 2px solid var(--border-color);
            border-bottom: none;
            font-size: 18px;
            font-weight: 700;
            color: var(--accent-primary);
            padding-top: 15px;
            margin-top: 10px;
        }

        /* Payment Methods */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .payment-method {
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover,
        .payment-method.active {
            border-color: var(--accent-primary);
            background: rgba(232, 180, 188, 0.1);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div>
                    <h1>🛒 Sistema de Ventas</h1>
                    <p class="header-subtitle">Venta de productos y servicios</p>
                </div>
                <div class="sale-info">
                    <div class="sale-number">Transacción #20240415-001</div>
                    <div class="sale-date"><?php echo date('d/m/Y H:i:s'); ?></div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">
                        Vendedor: <?php echo $empleado_actual['nombre']; ?> | 
                        Sucursal: <?php echo $empleado_actual['sucursal_nombre']; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="main-layout">
            <!-- Left Column: Products and Client -->
            <div class="left-column">
                <!-- Client Selection -->
                <div class="section">
                    <div class="section-header">
                        <div class="section-title">👤 Cliente</div>
                        <button class="btn btn-secondary">
                            🔍 Buscar Cliente
                        </button>
                    </div>
                    
                    <div class="client-info-card">
                        <div style="text-align: center; padding: 20px; color: var(--text-muted);">
                            <div style="font-size: 48px; margin-bottom: 10px;">👤</div>
                            <div style="margin-bottom: 15px;">Cliente no seleccionado</div>
                            <button class="btn btn-primary">
                                🔍 Seleccionar Cliente
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="section">
                    <div class="section-header">
                        <div class="section-title">🛍️ Productos Disponibles</div>
                        <div style="font-size: 14px; color: var(--text-muted);">
                            <span id="cartCount">3</span> productos en stock
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <input type="text" class="search-input" 
                               placeholder="🔍 Buscar producto..." 
                               style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px;">
                    </div>

                    <div class="products-grid">
                        <?php foreach ($productos_ejemplo as $producto): ?>
                            <div class="product-card">
                                <div class="product-name"><?php echo $producto['prd_nombre']; ?></div>
                                <div class="product-price">$<?php echo number_format($producto['prd_precioCompra'] * 1.5, 2); ?></div>
                                <div style="font-size: 12px; color: var(--text-muted);">
                                    Stock: <?php echo $producto['prd_stockActual']; ?> <?php echo $producto['prd_unidadMedida']; ?>
                                </div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">
                                    <?php echo $producto['categoria']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart and Payment -->
            <div class="right-column">
                <!-- Cart -->
                <div class="section">
                    <div class="section-header">
                        <div class="section-title">🛒 Carrito de Compra</div>
                        <button class="btn btn-danger">
                            🗑️ Vaciar
                        </button>
                    </div>

                    <div class="cart-items">
                        <div class="cart-item">
                            <div style="flex: 1;">
                                <div style="font-weight: 600;">Tinte Rubio Dorado #8.3</div>
                                <div style="font-size: 12px; color: var(--text-muted);">$12.75/unidad</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-weight: 600; color: var(--accent-primary);">$25.50</span>
                                <button style="color: var(--danger-color); background: none; border: none; cursor: pointer;">×</button>
                            </div>
                        </div>
                        
                        <div class="cart-item">
                            <div style="flex: 1;">
                                <div style="font-weight: 600;">Shampoo Anticaspa (1L)</div>
                                <div style="font-size: 12px; color: var(--text-muted);">$21.30/litro</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-weight: 600; color: var(--accent-primary);">$21.30</span>
                                <button style="color: var(--danger-color); background: none; border: none; cursor: pointer;">×</button>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button class="btn btn-secondary" style="flex: 1;">
                            💰 Descuento
                        </button>
                        <button class="btn btn-primary" style="flex: 1;">
                            🧮 Calcular
                        </button>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="section">
                    <div class="section-header">
                        <div class="section-title">💰 Resumen de Pago</div>
                    </div>

                    <div class="summary-grid">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$46.80</span>
                        </div>
                        <div class="summary-row">
                            <span>Descuento:</span>
                            <span>$0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>IVA (12%):</span>
                            <span>$5.62</span>
                        </div>
                        <div class="summary-row total">
                            <span>TOTAL:</span>
                            <span>$52.42</span>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div style="margin-top: 20px;">
                        <div style="font-weight: 600; margin-bottom: 10px;">💳 Método de Pago</div>
                        <div class="payment-methods">
                            <?php foreach ($metodos_pago as $key => $metodo): ?>
                                <div class="payment-method">
                                    <div style="font-size: 24px;">
                                        <?php echo explode(' ', $metodo)[0]; ?>
                                    </div>
                                    <div style="font-size: 12px; font-weight: 600;">
                                        <?php echo substr($metodo, strpos($metodo, ' ') + 1); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Complete Sale -->
                    <button class="btn btn-success" style="width: 100%; margin-top: 20px; padding: 15px;">
                        ✅ COMPLETAR VENTA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Modal -->
    <div class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="font-size: 20px; font-weight: 600;">🔍 Buscar Cliente</div>
                <div style="font-size: 24px; cursor: pointer;">×</div>
            </div>
            
            <input type="text" placeholder="Buscar por nombre, cédula..." 
                   style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 20px;">
            
            <!-- Client results would go here -->
            
            <button class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                👤 Continuar como Cliente General
            </button>
        </div>
    </div>

    <script>
        // Esta es solo la vista estática
        // La funcionalidad completa necesitaría:
        
        // 1. Conexión a base de datos real
        // 2. Funciones PHP para:
        //    - Obtener productos de Tbl_Producto
        //    - Buscar clientes en Tbl_Cliente
        //    - Calcular precios e impuestos
        //    - Insertar venta en Tbl_Venta
        //    - Actualizar stock en Tbl_Producto
        
        // 3. Funciones JavaScript para:
        //    - Agregar/remover productos del carrito
        //    - Calcular totales en tiempo real
        //    - Validar stock disponible
        //    - Manejar métodos de pago
        //    - Generar recibo/factura
        
        // 4. Tablas adicionales necesarias:
        //    - Tbl_DetalleVenta (para registrar productos vendidos)
        //    - Tbl_InventarioMovimiento (para tracking de stock)
        
        console.log('Vista de sistema de ventas - Implementación necesaria:');
        console.log('1. Backend PHP para procesar ventas');
        console.log('2. JavaScript para interactividad del carrito');
        console.log('3. Integración con base de datos NewStyle');
    </script>
</body>
</html>