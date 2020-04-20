/**
 * Created with JetBrains PhpStorm.
 * User: xuheng
 * Date: 12-5-22
 * Time: 涓婂崍11:38
 * To change this template use File | Settings | File Templates.
 */
var scrawl = function (options) {
    options && this.initOptions(options);
};
(function () {
    var canvas = $G("J_brushBoard"),
        context = canvas.getContext('2d'),
        drawStep = [], //undo redo瀛樺偍
        drawStepIndex = 0; //undo redo鎸囬拡

    scrawl.prototype = {
        isScrawl:false, //鏄