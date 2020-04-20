<?php
/**
 * 鑾峰彇宸蹭笂浼犵殑鏂囦欢鍒楄〃
 * User: Jinqn
 * Date: 14-04-09
 * Time: 涓婂崍10:17
 */
include "Uploader.class.php";

/* 鍒ゆ柇绫诲瀷 */
switch ($_GET['action']) {
    /* 鍒楀嚭鏂囦欢 */
    case 'listfile':
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];
        break;
    /* 鍒楀嚭鍥剧墖 */
    case 'listimage':
    default:
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

/* 鑾峰彇鍙傛暟 */
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
$end = $start + $size;

/* 鑾峰彇鏂囦欢鍒楄〃 */
$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
$files = getfiles($path, $allowFiles);
if (!count($files)) {
    return json_encode(array(
        "state" => "no match file",
        "list" => array(),
        "start" => $start,
        "total" => count($files)
    ));
}

/* 鑾峰彇鎸囧畾鑼冨洿鐨勫垪琛