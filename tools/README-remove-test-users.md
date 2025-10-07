Instrucciones para eliminar usuarios de prueba y re-seed la base de datos

Importante: Estas operaciones son destructivas. Haz una copia de seguridad de la base de datos antes de continuar.

1) Ver usuarios de prueba actuales
- Accede a la BD (phpMyAdmin o mysql client) y ejecuta:
  SELECT id, username, email, role, status, created_at FROM usuarios ORDER BY created_at DESC LIMIT 200;

2) Eliminar usuarios específicos por username o email
- Para eliminar por username:
  DELETE FROM usuarios WHERE username IN ('ADMINISTRADOR','FISCAL','VENTA');

- Para eliminar por email de ejemplo:
  DELETE FROM usuarios WHERE email LIKE '%@example.com';

3) Reiniciar los seeds (opción más limpia en entorno de desarrollo)
- En un entorno local con Laravel artisan disponible, puedes recrear la BD y ejecutar seeders:
  php artisan migrate:fresh --seed

  Nota: Esto eliminará todas las tablas y datos, y volverá a aplicar migraciones + seeders.

4) Si solo deseas ejecutar el seeder de usuarios (no recomendable en producción):
  php artisan db:seed --class=Database\\Seeders\\UserSeeder

5) Verificar
- Después de limpiar, revisa la tabla:
  SELECT id, username, email, role, status FROM usuarios ORDER BY created_at DESC LIMIT 100;

6) Alternativa: eliminar manualmente los usuarios 'pending' creados por registros recientes
- Si quieres mantener aprobados pero eliminar pendientes de prueba:
  DELETE FROM usuarios WHERE status = 'pending' AND email LIKE '%@example.com';

Si quieres, puedo ejecutar un script PHP/SQL que elimine las cuentas de prueba conocidas. Dime si prefieres eliminar por username, por email, o limpiar todos los usuarios pendientes.
