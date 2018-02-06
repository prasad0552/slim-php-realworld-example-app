$(document).ready(function(){
    $('body').on('click','.apply-discount',function(){
        var dtid = $(this).attr('data-id');
        jQuery.ajax({
            type: "PUT",
            url:  basuri+"/admin/discount",
            data: {id:dtid}, // serializes the form's elements.
            success: function(data) {
                console.log(data); // show response from the php script. (use the developer toolbar console, firefox firebug or chrome inspector console)
                //location.href = basuri+'/admin/discounts';
            }
        });
    });
});
