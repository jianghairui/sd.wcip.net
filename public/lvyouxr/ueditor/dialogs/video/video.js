/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-2-20
 * Time: 涓婂崍11:19
 * To change this template use File | Settings | File Templates.
 */

(function(){

    var video = {},
        uploadVideoList = [],
        isModifyUploadVideo = false,
        uploadFile;

    window.onload = function(){
        $focus($G("videoUrl"));
        initTabs();
        initVideo();
        initUpload();
    };

    /* 鍒濆