# Pasos para sincronizar BD y Código PHP

## 1. EJECUTAR SCRIPT SQL EN PHPMYADMIN

Abre `database_fixes.sql` y ejecuta el contenido en phpMyAdmin para:
- ✅ Agregar columnas `puntos` y `porcentaje` a `progreso_lecciones`
- ✅ Agregar columna `grado` a `usuarios_perfiles`
- ✅ Crear tabla `usuariosnuevos` (para compatibilidad legacy)

## 2. CAMBIOS EN ARCHIVOS PHP REALIZADOS

Se corrigieron 12 archivos PHP para usar nombres de columnas consistentes (snake_case):

### Cambios en Queries:
| Archivo | Cambio |
|---------|--------|
| `guardarComentarioMaestro.php` | `idMaestro` → `id_maestro`, `idAlumno` → `id_alumno` |
| `obtenerComentariosMaestro.php` | Actualizado a usar `usuarios` + `usuarios_perfiles` |
| `obtenerComentariosAlumno.php` | Actualizado a usar `usuarios` + `usuarios_perfiles` |
| `obtenerDatosAlumno.php` | Actualizado a usar `usuarios` + `usuarios_perfiles` |
| `obtenerAlumnosMaestro.php` | Actualizado a usar `usuarios` + `usuarios_perfiles` |
| `obtenerDatosMaestro.php` | Actualizado a usar `usuarios` + `usuarios_perfiles` |
| `maestroVerGrupo.php` | Actualizado a usar `usuarios` + `usuarios_perfiles` |
| `maestroVerProgreso.php` | Actualizado para usar `id_alumno` y `id_maestro` |
| `obtenerLogrosAlumno.php` | `completado` → `completada`, `porcentaje` → `progreso`, `idAlumno` → `id_alumno` |
| `obtenerProgresoAlumno.php` | Igual que arriba + alias de columna |
| `maestroVerProgreso.php` (SELECT) | `idMaestro` → `id_maestro`, `idAlumno` → `id_alumno` |

## 3. VERIFICACIÓN

Después de ejecutar el SQL y confirmar que los cambios en PHP se han guardado:

1. Recarga la página en el navegador (Ctrl+F5 para limpiar caché)
2. Intenta completar una lección como alumno
3. Comprueba que el progreso se guarde sin errores
4. Accede como maestro y verifica que vea los alumnos y comentarios

## 4. PRÓXIMOS PASOS (Recomendados)

- [ ] Eliminar tabla `usuariosnuevos` una vez que todo funcione (requiere migrar datos a `usuarios` + `usuarios_perfiles`)
- [ ] Consolidar variables de sesión: usar `$_SESSION['user_id']` y `$_SESSION['user_rol']` consistentemente
- [ ] Agregar más validaciones en PHP para evitar SQL injection

