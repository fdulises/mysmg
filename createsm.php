<?php
    /*
    Generador de sitemap.xml para sitios con archivos estáticos
    */

    //Ubicación del directorio a analizar
    define('PATH_DIR', '../neuralpin');

    //Ubicación y nombre del sitemap resultante
    define('SITEMAP_OUTPUT', 'sitemap.xml');

    //Ubicación y nombre del sitemap resultante
    define('SITE_DOMAIN', 'https://neuralpin.com');

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

    //Obtenemos listado de paginas a añadir al sitemap
    $list = list_files(PATH_DIR);

    //Obtenemos la plantilla xml para el sitemap
    $xmltemplate = file_get_contents('template.xml');

    //obtenemos el elemento xml para las <ulr>
	preg_match('/<url>(.*)<\/url>/is',$xmltemplate,$url);

    //Función para Generar <url>
    function genetareLink( string $link ): string{
        global $url;
        $newurl = preg_replace('/\[url\]/i', SITE_DOMAIN.'/'.$link, $url[0]);
        return $newurl;
    }

    //Función para generar listado de <url>
    function parseList( array $data, string $parent = '' ): array{
        static $links = [];
        foreach( $data as $k => $i ){
            if( is_string($i) ) $links[] = genetareLink("{$parent}{$i}");
            else parseList( $i, "{$k}/" );
        }
        return $links;
    }

    //Generamos listado de elementos <url>
    $urllist = parseList($list);

    //Remplazamos listado en la plantilla XML
    $xmltemplate = preg_replace('/<url>(.*)<\/url>/is', implode('',$urllist), $xmltemplate);

    //Generamos sitemap.xml en la ruta especificada
    $result = file_put_contents(SITEMAP_OUTPUT, $xmltemplate);

    //Mostramos resultado
    if( $result ) echo 'Sitemap generado correctamente en: ', SITEMAP_OUTPUT;
    else 'Ocurrió un error al generar Sitemap';