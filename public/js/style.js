// ハンバーガー
var state = false;
var scrollpos;
$(function () {
    $('.js-btn').on('click', function () { // js-btnクラスをクリックすると、
        $('.menu , .burger , .btn-line').toggleClass('open'); // メニューとバーガーの線にopenクラスをつけ外しする
        if (state == false) {
            scrollpos = $(window).scrollTop();
            $('body').addClass('fixed').css({ 'top': -scrollpos });
            state = true;
        } else {
            $('body').removeClass('fixed').css({ 'top': 0 });
            window.scrollTop(0, scrollpos);
            state = false;
        }
    })
});

// ページトップボタン
$(function () {
    // ボタンをクリックしたら、スクロールして上に戻る
    $('js-pagetop').on('click', function () {
        $('body,html').animate({
            scrollTop: 0
        }, 500);
        return false;
    });
});

