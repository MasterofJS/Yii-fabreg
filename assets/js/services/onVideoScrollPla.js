/*
function handlerScroll(a) {

    var index;
    const top = $(this).scrollTop();
    const height = window.innerHeight;

    index = a.map(function (c, i) {
    console.log(top, c, height);
        return (c - height >= top && c <= top) && i;
    });
    console.log(index);
}

function VideoScroll() {


    VideoScroll.videos = $();
    VideoScroll.videosTop = [];

}

VideoScroll.prototype.init = function () {

    VideoScroll.handlerScroll = handlerScroll.bind(VideoScroll, VideoScroll.videosTop);
    $(window).on('scroll', VideoScroll.handlerScroll);

};

VideoScroll.prototype.push = function (el) {

    const el = $(el);
    VideoScroll.videos = VideoScroll.videos.add(el);
    VideoScroll.videosTop.push(el.offset().top);

};


VideoScroll.prototype.destroy = function () {

    VideoScroll.videos = $();
    VideoScroll.videosTop = [];
    $(window).off('scroll', VideoScroll.handlerScroll);

};


////======= todo remove
VideoScroll.prototype._getLength = function () {
    console.log(VideoScroll.videos.length);
}

const LIST = new VideoScroll();

global.VVV = LIST;

export default LIST;
*/


