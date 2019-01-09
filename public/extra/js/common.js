$(function () {
   $('.boxmanage').on('click',function () {
       var check = $(this).is(':checked');
       $('.checks').each(function () {
           if(check){
               $(this).parent().addClass('checked');
           }else{
               $(this).parent().removeClass('checked');
           }
       });
   });
   
   $('.checks').on('click',function () {
       var check = $(this).parent().hasClass('checked');
       if(!check){
           $(this).parent().removeClass('checked');
           alert(check);
           //$('.boxmanage').parent().removeClass('checked');
       }
   });
});