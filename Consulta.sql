CREATE TABLE Persona (
    DNI_persona VARCHAR(20) PRIMARY KEY,
    nombre_persona VARCHAR(100) NOT NULL,
    apellido_persona VARCHAR(100) NOT NULL,
    email_persona VARCHAR(150) NOT NULL,
    telefono_persona VARCHAR(30),
    fechaNac_persona DATE
);


CREATE TABLE NivelProfesor (
    ID_Nprofesor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_nivelP VARCHAR(100) NOT NULL,
    Certificacion VARCHAR(150) NOT NULL
);


CREATE TABLE Profesores (
    ID_profesor INT AUTO_INCREMENT PRIMARY KEY,
    DNI_persona VARCHAR(20) NOT NULL UNIQUE,
    disponibilidad VARCHAR(100),
    salario_profesor INT,
    cargaHoraria INT,

    FOREIGN KEY (DNI_persona) REFERENCES Persona(DNI_persona)
);



CREATE TABLE NivelCliente (
    ID_nvCliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_nivelC VARCHAR(100) NOT NULL,
    descripcion TEXT
);


CREATE TABLE Cliente (
    ID_cliente INT AUTO_INCREMENT PRIMARY KEY,
    DNI_persona VARCHAR(20) NOT NULL UNIQUE,

    FOREIGN KEY (DNI_persona) REFERENCES Persona(DNI_persona)
);

ALTER TABLE Persona ADD UNIQUE (email_persona);
ALTER TABLE Persona MODIFY telefono_persona VARCHAR(30) NOT NULL;
ALTER TABLE Persona MODIFY fechaNac_persona DATE NOT NULL;
ALTER TABLE Persona ADD COLUMN password_persona VARCHAR(255) NOT NULL;
ALTER TABLE Persona ADD COLUMN activo BOOLEAN NOT NULL DEFAULT TRUE;
ALTER TABLE Persona ADD COLUMN rol_persona ENUM('admin','profesor','alumno') NOT NULL DEFAULT 'alumno';

ALTER TABLE Profesores ADD COLUMN ID_Nprofesor INT;
ALTER TABLE Profesores ADD FOREIGN KEY (ID_Nprofesor) REFERENCES NivelProfesor(ID_Nprofesor);

ALTER TABLE Cliente ADD COLUMN ID_nvCliente INT;
ALTER TABLE Cliente ADD FOREIGN KEY (ID_nvCliente) REFERENCES NivelCliente(ID_nvCliente);


SHOW CREATE TABLE Persona;

UPDATE Persona SET activo = true WHERE DNI_persona = '41234567';

CREATE TABLE Curso (
	ID_curso INT AUTO_INCREMENT PRIMARY KEY,
	nombre_curso VARCHAR(255) NOT NULL,
	descripcion TEXT,
	activo BOOLEAN NOT NULL DEFAULT TRUE
);


CREATE TABLE Horarios (
    ID_horarios INT AUTO_INCREMENT PRIMARY KEY,
    ID_profesor INT NOT NULL,
    ID_curso INT NOT NULL,
    aula_horarios VARCHAR(50) NOT NULL,
    horaInicio_horarios TIME NOT NULL,
    horaFin_horarios TIME NOT NULL,
    DiaSemana_horarios VARCHAR(20) NOT NULL,

    FOREIGN KEY (ID_profesor) REFERENCES Profesores(ID_profesor),
    FOREIGN KEY (ID_curso) REFERENCES Curso(ID_curso)
);

CREATE TABLE Inscripcion (
    ID_inscripcion INT AUTO_INCREMENT PRIMARY KEY,
    ID_cliente INT NOT NULL,
    ID_horarios INT NOT NULL,
    fechaAlta_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activa', 'cancelada') DEFAULT 'activa',

    FOREIGN KEY (ID_cliente) REFERENCES Cliente(ID_cliente),
    FOREIGN KEY (ID_horarios) REFERENCES Horarios(ID_horarios),
    UNIQUE(ID_cliente, ID_horarios)
);

CREATE TABLE EstadoPago (
    ID_estadoPago INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estadoPago VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE TipoPago (
    ID_tipoPago INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tipoPago VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Pagos (
    ID_pago INT AUTO_INCREMENT PRIMARY KEY,
    ID_inscripcion INT NOT NULL,
    ID_estadoPago INT NOT NULL,
    ID_tipoPago INT NOT NULL,
    monto_pago INT NOT NULL,
    fecha_pago DATE NOT NULL,

    FOREIGN KEY (ID_inscripcion) REFERENCES Inscripcion(ID_inscripcion),
    FOREIGN KEY (ID_estadoPago) REFERENCES EstadoPago(ID_estadoPago),
    FOREIGN KEY (ID_tipoPago) REFERENCES TipoPago(ID_tipoPago)
);

UPDATE Persona SET rol_persona = 'admin' WHERE DNI_persona = '46393434';


