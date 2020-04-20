/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-1-30
 * Time: 涓嬪崍12:50
 * To change this template use File | Settings | File Templates.
 */



var wordImage = {};
//(function(){
var g = baidu.g,
	flashObj,flashContainer;

wordImage.init = function(opt, callbacks) {
	showLocalPath("localPath");
	//createCopyButton("clipboard","localPath");
	createFlashUploader(opt, callbacks);
	addUploadListener();
	addOkListener();
};

function hideFlash(){
    flashObj = null;
    flashContainer.innerHTML = "";
}
function addOkListener() {
	dialog.onok = function() {
		if (!imageUrls.length) return;
		var urlPrefix = editor.getOpt('imageUrlPrefix'),
            images = domUtils.getElementsByTagName(editor.document,"img");
        editor.fireEvent('saveScene');
		for (var i = 0,img; img = images[i++];) {
			var src = img.getAttribute("word_img");
			if (!src) continue;
			for (var j = 0,url; url = imageUrls[j++];) {
				if (src.indexOf(url.original.replace(" ","")) != -1) {
					img.src = urlPrefix + url.url;
					img.setAttribute("_src", urlPrefix + url.url);  //鍚屾椂淇