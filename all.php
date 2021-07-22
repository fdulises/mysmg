<?php
    header('Content-type: application/json');

    /* Listar todos los archivos de un directorio con PHP */
    function list_files( string $path ): array{
        
        $data = [];
        $path = realpath($path);

        foreach( new DirectoryIterator($path) as $f ){

            $path_info = pathinfo($f);
            
            #Evitamos los archivos '.', '..' que son accesos directos
            if( $path_info['basename'] != '.' && $path_info['basename'] != '..' ){

                #En caso de que se trate de un directorío usamos recursividad
                if( is_dir($path.'/'.$path_info['basename']) ){

                    $contain = list_files( $path.'/'.$path_info['basename'] );
                    if( $contain ) $data[$path_info['basename']] = $contain;

                #En caso de solo ser un archivo lo añadimos a la lista
                }else $data[$path_info['basename']] = $path_info['basename'];

            }
        }

        return $data;
    }

    $list = list_files('../neuralpin');

    echo json_encode($list);