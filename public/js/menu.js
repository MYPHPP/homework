$("#icon").change(function () {
    var choose = $(this).val();
    var con = $(this).prev().text();
    var icon = '<i class="'+choose+'"></i>';
    var text = con+icon;
    $(this).prev().html(text);
});