;
$(function($) {
    TouchSlide({
        slideCell:"#slideBox",
        effect:"leftLoop",
        titCell: ".hd ul",//开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层,如果未开启则为.hd ul li
        mainCell:".bd ul",
        autoPage: true,//自动分页
        autoPlay:true,
        interTime:3500 //隔多少毫秒后执行下一个效果
    });
});
