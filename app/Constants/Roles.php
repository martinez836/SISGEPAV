<?php

namespace App\Constants;

class Roles
{
    // Definición de constantes para los roles de usuario con su ID correspondiente en la base de datos
    const ADMINISTRADOR = 1;
    const RECOLECTOR = 2;
    const CLASIFICADOR = 3;
    const VENDEDOR = 4;

    // Mapa de roles para facilitar la obtención del nombre del rol a partir de su ID
    public const Role_Routes = [
        self::ADMINISTRADOR => '/dashboard',
        self::RECOLECTOR => '/harvester',
        self::CLASIFICADOR => '/classification',
        self::VENDEDOR => '/dashboard',
    ];
}