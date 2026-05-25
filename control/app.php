<?php
define("ROOT_DIR", dirname(__DIR__));
define("PROFILE_UPLOAD_DIR",  ROOT_DIR . "/uploads/profile/");
define("PROFILE_UPLOAD_WEB",  "../uploads/profile/");
define("MEDICINE_UPLOAD_DIR", ROOT_DIR . "/uploads/medicines/");
define("MEDICINE_UPLOAD_WEB", "../uploads/medicines/");
define("REMEMBER_SECRET", "wtproject_task1_23_50088_1");

function ensureUploadDir($dir){
    if(!is_dir($dir)){
        mkdir($dir, 0755, true);
    }
}
?>
