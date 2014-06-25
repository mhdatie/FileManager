<?php

class Category {

    function get_category($link) {
        $fileTypes = array(
            // Image formats
            'image/jpeg' =>"image",
            'image/gif' =>"image",
            'image/png'=>"image",
            'image/bmp'=>"image",
            'image/tiff'=>"image",
            'image/x-icon'=>"image",
            // Video formats
            'video/asf'=>"video",
            'video/avi'=>"video",
            'video/divx'=>"video",
            'video/x-flv'=>"video",
            'video/quicktime'=>"video",
            'video/mpeg'=>"video",
            'video/mp4'=>"video",
            'video/ogg'=>"video",
            'video/x-matroska'=>"video",
            // Text formats
            'text/plain'=>"simple-document",
            'text/csv'=>"simple-document",
            'text/tab-separated-values'=>"simple-document",
            'text/calendar'=>"simple-document",
            'text/richtext'=>"simple-document",
            'text/css'=>"simple-document",
            'text/html'=>"simple-document",
            // Audio formats
            'audio/mpeg'=>"audio",
            'audio/x-realaudio'=>"audio",
            'audio/wav'=>"audio",
            'audio/ogg'=>"audio",
            'audio/midi'=>"audio",
            'audio/wma'=>"audio",
            'audio/x-matroska'=>"audio",
            // Misc application formats
            'application/rtf'=>"application",
            'application/javascript'=>"application",
            'application/pdf'=>"application",
            'application/x-shockwave-flash'=>"application",
            'application/java'=>"application",
            'application/x-tar'=>"application",
            'application/zip'=>"application",
            'application/x-gzip'=>"application",
            'application/rar'=>"application",
            'application/x-7z-compressed'=>"application",
            // MS Office formats
            'application/msword'=>"document",
            'application/vnd.ms-powerpoint'=>"document",
            'application/vnd.ms-write'=>"document",
            'application/vnd.ms-excel'=>"document",
            'application/vnd.ms-access'=>"document",
            'application/vnd.ms-project'=>"document",
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>"document",
            'application/vnd.ms-word.document.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template'=>"document",
            'application/vnd.ms-word.template.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>"document",
            'application/vnd.ms-excel.sheet.macroEnabled.12'=>"document",
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template'=>"document",
            'application/vnd.ms-excel.template.macroEnabled.12'=>"document",
            'application/vnd.ms-excel.addin.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'=>"document",
            'application/vnd.ms-powerpoint.presentation.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow'=>"document",
            'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.presentationml.template'=>"document",
            'application/vnd.ms-powerpoint.template.macroEnabled.12'=>"document",
            'application/vnd.ms-powerpoint.addin.macroEnabled.12'=>"document",
            'application/vnd.openxmlformats-officedocument.presentationml.slide'=>"document",
            'application/vnd.ms-powerpoint.slide.macroEnabled.12'=>"document",
            'application/onenote'=>"document",
            // OpenOffice formats
            'application/vnd.oasis.opendocument.text'=>"document",
            'application/vnd.oasis.opendocument.presentation'=>"document",
            'application/vnd.oasis.opendocument.spreadsheet'=>"document",
            'application/vnd.oasis.opendocument.graphics'=>"document",
            'application/vnd.oasis.opendocument.chart'=>"document",
            'application/vnd.oasis.opendocument.database'=>"document",
            'application/vnd.oasis.opendocument.formula'=>"document",
            // WordPerfect formats
            'application/wordperfect'=>"document",
        );

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $key = finfo_file($finfo, $link);
        
        $value = $fileTypes[$key];
        return $value;
     }

}

?>
