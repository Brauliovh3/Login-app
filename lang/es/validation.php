<?php
return [
    'unique' => 'El campo :attribute ya ha sido tomado.',
    'in' => 'El :attribute seleccionando es valido.',
    
    'custom' => [
        'username' => [
            'unique' => 'El nombre ya esta en uso.',
        ],
        'email' => [
            'unique' => 'El correo electronico ya esta en uso.',
        ],
        'role' => [
            'in' => 'El rol seleccionado no es valido.',
        ],
    ],
    'attributes' => [
        'username' => 'nombre de usuario',
        'email' => 'correo electrónico',
        'role' => 'rol',
        'password' => 'contraseña',
        'name' => 'nombre',
        // ...otros atributos personalizados...
    ],
];