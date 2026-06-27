-- base de datos plataforma de aprendizaje
-- hecha por: [tu nombre]
-- fecha: abril 2026

create database plataforma_aprendizaje;
use plataforma_aprendizaje;


-- tabla rol
create table ROL(
	id_rol int primary key auto_increment,
    nombre_rol varchar(50) not null,
    descripcion varchar(200)
);

insert into ROL values (1, 'Administrador', 'tiene acceso a todo');
insert into ROL values (2, 'Estudiante', 'puede ver lecciones y hacer ejercicios');
insert into ROL values (3, 'Instructor', 'puede crear lecciones');
insert into ROL values (4, 'Moderador', 'puede gestionar usuarios');
insert into ROL values (5, 'Invitado', 'solo puede ver contenido');


-- tabla usuario
create table USUARIO(
	id_usuario int primary key auto_increment,
    nombre varchar(100) not null,
    correo varchar(150) not null,
    contrasena varchar(255) not null,
    id_rol int,
    foreign key (id_rol) references ROL(id_rol)
);

insert into USUARIO values (1, 'Carlos Perez', 'carlos@gmail.com', '1234', 2);
insert into USUARIO values (2, 'Ana Gomez', 'ana@gmail.com', '5678', 2);
insert into USUARIO values (3, 'Luis Rodriguez', 'luis@gmail.com', 'abcd', 3);
insert into USUARIO values (4, 'Maria Torres', 'maria@gmail.com', 'efgh', 1);
insert into USUARIO values (5, 'Juan Martinez', 'juan@gmail.com', 'ijkl', 2);
insert into USUARIO values (6, 'Sofia Herrera', 'sofia@gmail.com', 'mnop', 4);


-- tabla leccion
create table LECCION(
	id_leccion int primary key auto_increment,
    titulo varchar(150) not null,
    descripcion varchar(300),
    nivel varchar(50)
);

insert into LECCION values (1, 'Introduccion a la Programacion', 'conceptos basicos', 'Basico');
insert into LECCION values (2, 'Variables y tipos de datos', 'int float string etc', 'Basico');
insert into LECCION values (3, 'Estructuras de control', 'if else for while', 'Intermedio');
insert into LECCION values (4, 'Programacion orientada a objetos', 'clases objetos herencia', 'Intermedio');
insert into LECCION values (5, 'Bases de datos', 'tablas relaciones claves', 'Avanzado');
insert into LECCION values (6, 'Algoritmos de ordenamiento', 'bubble sort quicksort', 'Avanzado');


-- tabla video
create table VIDEO(
	id_video int primary key auto_increment,
    titulo varchar(150),
    duracion int  -- en segundos
);

insert into VIDEO values (1, 'que es un algoritmo', 480);
insert into VIDEO values (2, 'tipos de datos en python', 360);
insert into VIDEO values (3, 'el bucle for', 540);
insert into VIDEO values (4, 'clases en java', 720);
insert into VIDEO values (5, 'diseño de base de datos', 600);
insert into VIDEO values (6, 'quicksort explicado', 450);


-- tabla ejercicios
-- un ejercicio pertenece a una leccion (relacion 1:N)
create table EJERCICIOS(
	id_ejercicio int primary key auto_increment,
    pregunta text not null,
    respuesta text not null,
    id_leccion int,
    foreign key (id_leccion) references LECCION(id_leccion)
);

insert into EJERCICIOS values (1, 'que es un algoritmo?', 'es una secuencia de pasos para resolver un problema', 1);
insert into EJERCICIOS values (2, 'diferencia entre int y float?', 'int es entero y float tiene decimales', 2);
insert into EJERCICIOS values (3, 'para que sirve el while?', 'repite codigo mientras la condicion sea true', 3);
insert into EJERCICIOS values (4, 'que es la herencia en poo?', 'cuando una clase hija hereda los atributos de la padre', 4);
insert into EJERCICIOS values (5, 'que es una llave primaria?', 'identifica de forma unica cada registro de una tabla', 5);
insert into EJERCICIOS values (6, 'complejidad de quicksort?', 'O(n log n) en el caso promedio', 6);


-- tabla intermedia ejercicio_video (relacion N:N entre ejercicios y video)
create table EJERCICIO_VIDEO(
	id_ejercicio int,
    id_video int,
    primary key (id_ejercicio, id_video),
    foreign key (id_ejercicio) references EJERCICIOS(id_ejercicio),
    foreign key (id_video) references VIDEO(id_video)
);

insert into EJERCICIO_VIDEO values (1,1);
insert into EJERCICIO_VIDEO values (2,2);
insert into EJERCICIO_VIDEO values (3,3);
insert into EJERCICIO_VIDEO values (4,4);
insert into EJERCICIO_VIDEO values (5,5);
insert into EJERCICIO_VIDEO values (6,6);


-- tabla resultados
-- un usuario recibe muchos resultados (1:N)
-- un ejercicio da muchos resultados (N:1)
create table RESULTADOS(
	id_resultado int primary key auto_increment,
    fecha date,
    puntaje decimal(5,2),
    id_ejercicio int,
    id_usuario int,
    foreign key (id_ejercicio) references EJERCICIOS(id_ejercicio),
    foreign key (id_usuario) references USUARIO(id_usuario)
);

insert into RESULTADOS values (1, '2025-03-01', 100.00, 1, 1);
insert into RESULTADOS values (2, '2025-03-02', 80.00, 2, 1);
insert into RESULTADOS values (3, '2025-03-03', 90.00, 3, 2);
insert into RESULTADOS values (4, '2025-03-04', 70.00, 4, 2);
insert into RESULTADOS values (5, '2025-03-05', 85.00, 5, 5);
insert into RESULTADOS values (6, '2025-03-06', 95.00, 6, 5);
insert into RESULTADOS values (7, '2025-03-07', 60.00, 1, 6);


-- tabla leccion_usuario para saber que lecciones hizo cada usuario
create table LECCION_USUARIO(
	id_leccion int,
    id_usuario int,
    fecha_inicio date,
    completada tinyint(1) default 0,
    primary key (id_leccion, id_usuario),
    foreign key (id_leccion) references LECCION(id_leccion),
    foreign key (id_usuario) references USUARIO(id_usuario)
);

insert into LECCION_USUARIO values (1, 1, '2025-03-01', 1);
insert into LECCION_USUARIO values (2, 1, '2025-03-02', 1);
insert into LECCION_USUARIO values (3, 1, '2025-03-05', 0);
insert into LECCION_USUARIO values (1, 2, '2025-03-01', 1);
insert into LECCION_USUARIO values (2, 2, '2025-03-03', 0);
insert into LECCION_USUARIO values (4, 5, '2025-03-04', 1);
insert into LECCION_USUARIO values (5, 6, '2025-03-06', 0);
