-- Ver todas las actas sin filtros
SELECT id, numero_acta, nombres_conductor, apellidos_conductor, placa, ruc_dni, estado 
FROM actas 
ORDER BY id DESC;
