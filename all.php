<?php

    /*
    Función para listar contenido de un directorio
    */
    function list_files( string $path ): array{

        $data = [];
        $path = realpath($path);

        foreach( new DirectoryIterator($path) as $f ){
            
            //Evitamos los archivos '.', '..' que son accesos directos
            if( $f->getBasename() != '.' && $f->getBasename() != '..' ){

                //En caso de que se trate de un directorío usamos recursividad
                if( is_dir($path.'/'.$f->getBasename()) ){
                    $data[$f->getBasename()] = list_files( $path.'/'.$f->getBasename() );

                //En caso de solo ser un archivo lo añadimos a la lista
                }else{
                    $data[$f->getBasename()] = $f->getBasename();
                }

            }
        }

        return $data;
    }

    $list = list_files('');

    //Mostramos lista de archivos en formato JSON
    header('Content-type: application/json');
    echo json_encode($list);