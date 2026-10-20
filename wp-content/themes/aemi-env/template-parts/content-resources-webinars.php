<?php

switch ($args['type_recurso']):
    case 'link':
        $icon = '<i class="fa-solid fa-arrow-up-right-from-square"></i>';
        $content = '<a target="_blank" href="'.$args['link_recurso'].'">'.$args['link_recurso'].'</a>';
        break;

    case 'text':
        $icon = '<i class="fa-solid fa-file-word"></i>';
        $content = '<span>'.$args['texto_recurso'].'</span>';
        break;

    case 'files':
        $icon = '<i class="fa-solid fa-file"></i>';
        $content = '<a target="_blank" href="'.$args['files_recurso']['url'].'">'.$args['files_recurso']['filename'].'</a>';
        break;
endswitch;

?>

<li><?=$icon?> <?=$content?></li>