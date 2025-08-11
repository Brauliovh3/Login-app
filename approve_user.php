<?php

use App\Models\User;

$user = User::find(7);
if ($user) {
    $user->approval_status = 'approved';
    $user->status = 'approved';
    $user->approved_at = now();
    $user->approved_by = 1;
    $user->save();
    
    echo "Usuario {$user->name} aprobado exitosamente!" . PHP_EOL;
    echo "Email: {$user->email}" . PHP_EOL;
    echo "Status: {$user->status}" . PHP_EOL;
    echo "Approval Status: {$user->approval_status}" . PHP_EOL;
} else {
    echo "Usuario no encontrado" . PHP_EOL;
}
