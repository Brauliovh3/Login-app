<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class ModuleTokenHelper
{
    /**
     * Mapeo de módulos con sus tokens
     */
    private static $moduleTokens = [
        'gestionar-usuarios' => 'usr_mgmt',
        'aprobar-usuarios' => 'usr_appr', 
        'infracciones' => 'infr_mgmt',
        'mantenimiento-conductores' => 'mnt_drv',
        'mantenimiento-inspectores' => 'mnt_insp',
    ];

    /**
     * Generar token encriptado para un módulo
     */
    public static function generateToken($module)
    {
        if (!isset(self::$moduleTokens[$module])) {
            return null;
        }
        
        $data = [
            'module' => $module,
            'token' => self::$moduleTokens[$module],
            'timestamp' => time()
        ];
        
        return Crypt::encryptString(json_encode($data));
    }

    /**
     * Decodificar token y obtener módulo
     */
    public static function decodeToken($encryptedToken)
    {
        try {
            $decrypted = Crypt::decryptString($encryptedToken);
            $data = json_decode($decrypted, true);
            
            if (!$data || !isset($data['module'], $data['token'])) {
                return null;
            }
            
            // Verificar que el token corresponde al módulo
            if (!isset(self::$moduleTokens[$data['module']]) || 
                self::$moduleTokens[$data['module']] !== $data['token']) {
                return null;
            }
            
            return $data['module'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Obtener todos los tokens de módulos
     */
    public static function getAllTokens()
    {
        $tokens = [];
        foreach (self::$moduleTokens as $module => $token) {
            $tokens[$module] = self::generateToken($module);
        }
        return $tokens;
    }
}