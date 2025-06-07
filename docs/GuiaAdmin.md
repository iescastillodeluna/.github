## Guía del administrador

La organización dispone de un único miembro con el rol *owner* para encargarse
de la administración.

### Funciones

Las funciones del administrador son:

1. Crear, archivar y eliminar las aulas que componen el [GitHub
   Classroom](https://classroom.github.com) del organización.
1. Agregar y eliminar profesores a la organización.
1. Vigilar el resultado de la ejecución automática de los *workflows* de
   almacenamiento y, en su caso, realizar alguna ejecución manual.

### Aulas

Los profesores tiene la responsabilidad de gestionar las aulas de las que son
administradores, pero son incapaces de crearlas o eliminarlas, por lo que esta
tarea recae en el administrador. En particular es importante tener presente:

* Los nombres de las aulas debería tener el siguiente formato:

  ``CODIGO -- Nombre del módulo``

  Por ejemplo:

  ``0486 -- Acceso a datos``

* El administrador de la organización es el encargado de registrar como
  administrador de aula al profesor encargado de impartir la asignatura o
  módulo asociado.

* Debería existir una aula por cada módulo o asignatura para la que se quieran
  usar repositorios de *GitHub*. También podría ser interesante mantener dos: el
  aula del curso pasado y la del presente; ya que la interfaz da la posibilidad
  de reutilizar la tarea de un curso en otro distinto, lo que ahora configurar
  la tarea si es exactamente igual que la del curso anterior.

  En caso de que se mantenga el aula del curso anterior se puede renombrar como:

  ``0486 -- Acceso a datos [ARCHIVO]``

  y marcarse como archivada.

+ El *bot* elimina automáticamente los repositorios de estudiante al eliminarse
  la tarea a la que están asociados o bien un aula cuyo efecto es borrar en
  cascada todas las tareas incluidas en ella. Como eliminar tareas individuales
  es una acción muy rara probablemente asociada a algún error, los repositorios
  de estudiante permanecerán en la organización mientras exista el aula
  asociada. Por ello, es conveniente eliminar las aulas que no sean necesarias.

+ La gestión de aulas, esto es, la creación de tareas dentro del aula, debe ser
  una labor exclusiva del profesor responsable, no del administrador.

### Miembros

A comienzos de curso el administrador debería encargarse de cursar las altas y
bajas de los profesores. La baja de un profesor lleva aparejada la eliminación
de todos los repositorios que hubiera creado a menos que se hubieran cambiado
los permisos sobre el repositorio añadiendo otro miembro a ellos.

> **Nota**  
> El efecto del cambio de permisos no es inmediato, sino que se produce después
> de la ejecución del *workflow*
> [member-repo-watcher.yaml](../.github/workflows/member-repo-watcher.yaml), por
> lo que si se desea conservar un repositorio conviene no confiar exclusivamente
> en el cambio de permisos y eliminar a continuación al usuario. Lo que sí puede
> hacerse es forzar su ejecución manual y, a continuación, eliminar.

## Workflows

Consúltese el archivo [README.md](../README.md).
