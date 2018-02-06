$(document).ready(function(){
    $('body').on('click','.apply-discount',function(){
        var dtid = $(this).attr('data-id');
        var enabled = $(this).attr('data-enabled');
        var dis = $(this);
        jQuery.ajax({
            type: "PUT",
            url:  basuri+"/admin/discount",
            data: {id:dtid, enabled:enabled, onlyenable:'yes'}, // serializes the form's elements.
            success: function(data) {
                console.log(data); // show response from the php script. (use the developer toolbar console, firefox firebug or chrome inspector console)
                //location.href = basuri+'/admin/discounts';
                if(data.status=='true')
                {
                    if(data.resp==0)
                    {
                        dis.html('<i class="fa fa-ban"></i>')
                    }
                    else
                    {
                        dis.html('<i class="fa fa-check"></i>')
                    }
                }
            }
        });
    });

    $('body').on('click','.delete-discount',function(){
        if(!confirm("Are you sure you want to Delete."))
        {
            return false;
        }
        var dtid = $(this).attr('data-id');
        var dis = $(this);
        jQuery.ajax({
            type: "DELETE",
            url:  basuri+"/admin/discount",
            data: {id:dtid}, // serializes the form's elements.
            success: function(data) {
                console.log(data); // show response from the php script. (use the developer toolbar console, firefox firebug or chrome inspector console)
                if(data.status=='true')
                {
                    $('#discount-'+dtid).remove();
                }
            }
        });
    });

});
