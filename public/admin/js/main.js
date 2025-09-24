// $(document).ready(() => {
//     $('.custom-loader').show(1);
// });
//
// $(window).on("load", function () {
//     $('.custom-loader').fadeOut(1000);
// });

// ====== Dark Mode On Change ==========
$('#darkMode').on('change', function () {
    if ($(this).is(':checked')){
        localStorage.setItem('mode-type', 'dark-mode');
        $('body').addClass('dark-mode');
    } else {
        $('body').removeClass('dark-mode');
        localStorage.removeItem('mode-type');
    }
});

if (localStorage.getItem('mode-type') === 'dark-mode')
{
    $('#darkMode').prop('checked', true);
    $('body').addClass('dark-mode');
} else {
    $('#darkMode').prop('checked', false);
    $('body').removeClass('dark-mode');
}

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});







// ========= For Set Perfect height and position of aside and body content ==============
var getNavHeight = 0;
getNavHeight = $('#wrappingNav').outerHeight();
$('#wrappingAside').css({
    'height': 'calc(100% - '+ getNavHeight + 'px' + ')',
    'top':  getNavHeight + 'px',
});
$('#wrappingBody').css({'top':  getNavHeight + 'px'});

// alert(getNavHeight);

// $(window).resize(function () {
//     getNavHeight = $('#wrappingNav').outerHeight();
// });

// console.log(getNavHeight);

// =========== Panel Wrapping ============
$('.make-resize').on('click', function (e) {
    e.preventDefault();
    $('body').stop().toggleClass('resize-wrapper');

    if ($(window).width() < 992)
    {
        $('.panel-wrapper').stop().toggleClass('wrapping-show')
    }
});

// ======== Click Toggle Item =============
$(document).on('click', '.toggler', function (e) {
    e.preventDefault();
    let parentToggleGroup = $(this).parents('.toggleable-group');
    if (parentToggleGroup.length) {
        parentToggleGroup.find('.toggleable-content').not($(this).parents('.toggle-item').find('.toggleable-content')).slideUp().removeClass('show');
        parentToggleGroup.find('.toggler').not($(this)).removeClass('active');
    }
    $(this).stop().toggleClass('active');
    $(this).parents('.toggle-item').find('.toggleable-content').stop().slideToggle(300).queue(function () {
        $(this).toggleClass('show').dequeue();
    });
});





// ======= Nice Scroll ========
// if ($('.nice-scroll').length > 0)
// {
//     $('.nice-scroll').niceScroll();
// }
//
// // ===== Tooltip ======
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});


// ================ Dropdown Position for Table ============================
$(document).on('mouseover', '.action-dropdown-on-hover', function () {
    const dropdownToggle = new bootstrap.Dropdown(this);
    dropdownToggle.show();

    // dropdownElement.addEventListener('mouseleave', () => {
    //     dropdownToggle.hide();
    // });
});
$(document).on('mouseleave', '.dropdown', function () {
    const dropdownToggle = new bootstrap.Dropdown($(this).find('.action-dropdown-on-hover').get(0));
    dropdownToggle.hide();
});
