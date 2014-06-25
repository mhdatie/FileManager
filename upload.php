<?php

include './config/db.php';
session_start();

include 'class/confirm.php';

$category = new Category();

$user_id = $_SESSION['id'];

$filename = $_POST['filename'];
$description = $_POST['desc'];

$fileTypes = array(
    // Image formats
    'image/jpeg',
    'image/gif',
    'image/png',
    'image/bmp',
    'image/tiff',
    'image/x-icon',
    // Video formats
    'video/asf',
    'video/avi',
    'video/divx',
    'video/x-flv',
    'video/quicktime',
    'video/mpeg',
    'video/mp4',
    'video/ogg',
    'video/x-matroska',
    // Text formats
    'text/plain',
    'text/csv',
    'text/tab-separated-values',
    'text/calendar',
    'text/richtext',
    'text/css',
    'text/html',
    // Audio formats
    'audio/mpeg',
    'audio/x-realaudio',
    'audio/wav',
    'audio/ogg',
    'audio/midi',
    'audio/wma',
    'audio/x-matroska',
    // Misc application formats
    'application/rtf',
    'application/javascript',
    'application/pdf',
    'application/x-shockwave-flash',
    'application/java',
    'application/x-tar',
    'application/zip',
    'application/x-gzip',
    'application/rar',
    'application/x-7z-compressed',
    // MS Office formats
    'application/msword',
    'application/vnd.ms-powerpoint',
    'application/vnd.ms-write',
    'application/vnd.ms-excel',
    'application/vnd.ms-access',
    'application/vnd.ms-project',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-word.document.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
    'application/vnd.ms-word.template.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-excel.sheet.macroEnabled.12',
    'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
    'application/vnd.ms-excel.template.macroEnabled.12',
    'application/vnd.ms-excel.addin.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
    'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.presentationml.template',
    'application/vnd.ms-powerpoint.template.macroEnabled.12',
    'application/vnd.ms-powerpoint.addin.macroEnabled.12',
    'application/vnd.openxmlformats-officedocument.presentationml.slide',
    'application/vnd.ms-powerpoint.slide.macroEnabled.12',
    'application/onenote',
    // OpenOffice formats
    'application/vnd.oasis.opendocument.text',
    'application/vnd.oasis.opendocument.presentation',
    'application/vnd.oasis.opendocument.spreadsheet',
    'application/vnd.oasis.opendocument.graphics',
    'application/vnd.oasis.opendocument.chart',
    'application/vnd.oasis.opendocument.database',
    'application/vnd.oasis.opendocument.formula',
    // WordPerfect formats
    'application/wordperfect',
);

function dirSize($directory) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
        $size+=$file->getSize();
    }
    return $size;
}

if ($_POST['group'] == "public") {
    // Folder to upload files to. Must end with slash /
    define('DESTINATION_FOLDER', 'public/user_' . $user_id . '/');
} elseif ($_POST['group'] == "group") {
    //GROUP FOLDER
    $gid = $_POST['gid'];
    define('DESTINATION_FOLDER', 'private/group_' . $gid . '/user_' . $user_id . '/');
    $private = 1;
}

//phpinfo();

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['file']['tmp_name']);
//echo $mime;

if (in_array(strtolower($mime), $fileTypes)) {
    if ($_FILES["file"]["error"] > 0) {
        if ($_POST['from'] == "fromhome") {
            header("Location: homepage.php?ver=err");
            exit();
        } elseif ($_POST['from'] == "fromprofile") {
            header("Location: profile.php?uid=$user_id&ver=err");
            exit();
        } elseif ($_POST['from'] == "fromgroup") {
            header("Location: group.php?gid=$gid&ver=err");
            exit();
        }
    } else {

        $filesize = $_FILES["file"]["size"] / 1024 / 1024;
        $size = dirSize('public/user_' . $user_id . '/');
        $sizeM = $size / 1024 / 1024;

        $member_query = mysql_query("SELECT * FROM `group_has_members` WHERE user_id = $user_id");
        $private_sizeM = 0;
        if (mysql_num_rows($member_query) > 0) {
            while ($pdir = mysql_fetch_array($member_query)) {
                $private_size = dirSize($pdir['link']);
                $private_sizeM += $private_size / 1024 / 1024;
            }
        }

        if (($sizeM + $private_sizeM + $filesize) >= 5120) { //5120 MB php.ini
            header("Location: homepage.php?ver=limit");
            exit();
        }
        if (file_exists(DESTINATION_FOLDER . $_FILES["file"]["name"]) || file_exists(DESTINATION_FOLDER . $_FILES["file"]["name"] . ".zip")) {
            if ($_POST['from'] == "fromhome") {
                header("Location: homepage.php?ver=exist");
                exit();
            } elseif ($_POST['from'] == "fromprofile") {
                header("Location: profile.php?uid=$user_id&ver=exist");
                exit();
            } elseif ($_POST['from'] == "fromgroup") {
                header("Location: group.php?gid=$gid&ver=exist");
                exit();
            }
        } else {
            $type = $category->get_category($_FILES["file"]["tmp_name"]);
            if ($type == "document") {
                $zip = new ZipArchive();
                $filecompress = DESTINATION_FOLDER . $_FILES["file"]["name"] . ".zip";

                $compress = $zip->open($filecompress, ZIPARCHIVE::CREATE);
                if ($compress === true) {
                    $_FILES["file"]["name"] = str_replace("%", "", $_FILES["file"]["name"]); //replace % with empty string
                    move_uploaded_file($_FILES["file"]["tmp_name"], DESTINATION_FOLDER . $_FILES["file"]["name"]);
                    $zip->addFile(DESTINATION_FOLDER . $_FILES["file"]["name"], $_FILES["file"]["name"]);
                    $zip->close();

                    $link = DESTINATION_FOLDER . $_FILES["file"]["name"] . ".zip";
                    unlink(DESTINATION_FOLDER . $_FILES["file"]["name"]);
                }
            } else {
                $_FILES["file"]["name"] = str_replace("%", "", $_FILES["file"]["name"]);
                move_uploaded_file($_FILES["file"]["tmp_name"], DESTINATION_FOLDER . $_FILES["file"]["name"]);
                $link = DESTINATION_FOLDER . $_FILES["file"]["name"];
                // echo $_FILES['file']['error'];
            }

            if (empty($private)) {
                $query = "INSERT INTO files (user_id, name, description, group_id, deleted, link) VALUES ('$user_id','$filename','$description','0','0','$link')";
            } else {
                $query = "INSERT INTO files (user_id, name, description, group_id, deleted, link) VALUES ('$user_id','$filename','$description','$gid','0','$link')";
            }
            $result = mysql_query($query) or die(mysql_error());
        }
    }
} else {
    if ($_POST['from'] == "fromhome") {
        header("Location: homepage.php?ver=ns");
        exit();
    } elseif ($_POST['from'] == "fromprofile") {
        header("Location: profile.php?uid=$user_id&ver=ns");
        exit();
    } elseif ($_POST['from'] == "fromgroup") {
        header("Location: group.php?gid=$gid&ver=ns");
        exit();
    }
}

if ($_POST['from'] == "fromhome") {
    header("Location: homepage.php");
    exit();
} elseif ($_POST['from'] == "fromprofile") {
    header("Location: profile.php?uid=$user_id");
    exit();
} elseif ($_POST['from'] == "fromgroup") {
    header("Location: group.php?gid=$gid");
    exit();
}
?>
