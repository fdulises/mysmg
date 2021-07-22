<?php
    /*
    Generador de sitemap.xml para sitios con archivos estáticos
    */

    //Ubicación del directorio a analizar
    define('PATH_DIR', '../neuralpin');

    //Ubicación y nombre del sitemap resultante
    define('SITEMAP_OUTPUT', 'sitemap.xml');

    //Archivos o carpetas a evitar
    define('IGNORED_LIST', [
        realpath(PATH_DIR.'/404.html'),
        realpath(PATH_DIR.'/.git'),
        realpath(PATH_DIR.'/_cms'),
        realpath(PATH_DIR.'/theme'),
    ]);

    //Extensiones de los archivos a añadir al sitemap
    define('ALLOWED_EXT', ['html', 'htm']);

    /* Listar todos los archivos de un directorio con PHP */
    function list_files( string $path ): array{
        
        $data = [];
        $path = realpath($path);

        foreach( new DirectoryIterator($path) as $f ){
            
            if( 
                //Evitamos los archivos '.', '..' que son accesos directos
                $f->getBasename() != '.' && $f->getBasename() != '..'

                //Quitamos archivos/carpetas a ignorar
                && !in_array( $f->getPathname(), IGNORED_LIST )
            ){

                //En caso de que se trate de un directorío usamos recursividad
                if( is_dir($path.'/'.$f->getBasename()) ){

                    //Si el directorio estaba vacío no lo añadimos
                    $newlist = list_files( $path.'/'.$f->getBasename() );
                    if( $newlist ) $data[$f->getBasename()] = $newlist;

                //En caso de solo ser un archivo lo añadimos a la lista
                }else{
                    //Antes de añadirlo validamos que sea un tipo de archivo valido
                    if( in_array( $f->getExtension(), ALLOWED_EXT ) )
                        $data[$f->getBasename()] = $f->getBasename();
                }

            }
        }

        return $data;
    }

    $list = list_files(PATH_DIR);

    header('Content-type: application/json');
    echo json_encode($list);