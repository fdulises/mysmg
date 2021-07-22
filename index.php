<?php
    header('Content-type: application/json');

    define('PATH_SITE', '../neuralpin');
    define('PATH_SMG_OUTPUT', '');
    define('SMG_NAME', 'sitemap.xml');

    define('IGNORED_LIST', ['404.html']);
    define('ALLOWED_LIST', ['html', 'htm']);

    function list_files( string $path ): array{
        $data = [];
        $path = realpath($path);
        
        foreach( glob($path.'/*') as $f ){
            $path_info = pathinfo($f);

            if( is_dir($f) ){
                $path_info['contain'] = list_files( $path.'/'.$path_info['basename'] );
                if( $path_info['contain'] ) $data[$path_info['basename']] = $path_info['contain'];
            }else if( 
                in_array( $path_info['extension'], ALLOWED_LIST ) && 
                !in_array( $path_info['basename'], IGNORED_LIST ) 
            ) $data[$path_info['basename']] = $path_info['basename'];
            
        }
        return $data;
    }

    $list = list_files(PATH_SITE);

    echo json_encode($list);