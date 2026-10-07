<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Usuario;

echo "=== CONVIRTIENDO CONTRASEÑAS ===\n\n";

// Contraseña estándar para todos (puedes cambiar)
$standard_password = 'cliente123';

$usuarios = Usuario::all();
$convertidos = 0;

foreach ($usuarios as $usuario) {
    $hash_nuevo = base64_encode(hash('sha256', $standard_password, true));
    
    echo "Usuario: {$usuario->usr_email}\n";
    echo "Hash anterior: " . substr($usuario->usr_password, 0, 30) . "...\n";
    
    $usuario->usr_password = $hash_nuevo;
    $usuario->save();
    
    echo "Hash nuevo: " . substr($hash_nuevo, 0, 30) . "...\n";
    echo "---\n";
    
    $convertidos++;
}

echo "\n✅ Convertidos: $convertidos usuarios\n";
echo "Contraseña estándar: '$standard_password'\n";
echo "\nPara probar login con cualquier usuario:\n";
echo "Email: cualquier usuario de la lista\n";
echo "Password: cliente123\n";