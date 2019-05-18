owner.addSwiper= function ($el) {
    var i = $(window.editor + ' [data-editor-swiper]').find('[data-item]').last().data('item') + 1;
    var html = '<div data-item="'+i+'" class="uk-input-group uk-margin-bottom"><input class="uk-input" type="text" name="data['+i+'][image]" id="menu-image-'+i+'" value="" placeholder="轮播图片"><span class="uk-input-group-btn"><button class="uk-button uk-button-default" type="button" data-type="image" data-dux="system-attach" data-target="#menu-image-'+i+'"><i class="fa fa-upload"></i></button></span><input class="uk-input" type="text" name="data['+i+'][url]" value="" placeholder="图片链接"><span class="uk-input-group-btn"><button class="uk-button uk-button-default" data-del type="button">删除</button></span></div>';
    var htmlEl = $(html);
    $(window.editor + ' [data-editor-swiper]').append(htmlEl);
    var attach = htmlEl.find('[data-dux="system-attach"]');
    system.attach(attach, $(attach).data());
};