$(document).ready(function(){

    // Click event for sign in button
    $('body').on('click','.login-btn',function(){
        var $loginForm = $('#login_form');
        var hasErr = validateForm($loginForm);
        var username = $loginForm.find('.username').val();
        var password = $loginForm.find('.password').val();
        if(!hasErr) {
            // Making ajax call to validate user and to store user data in session
            jQuery.ajax({
                type: "POST",
                url:  basuri+"/admin/login",
                data: {username:username, password:password}, // serializes the form's elements.
                success: function(data) {
                    console.log(data);
                    if(data.status === true)
                    {
                        if(data.isValid)
                        {
                            window.location.href = 'dashboard';
                        }
                    } else {
                            // Display error message above login form
                        $loginForm.find('.login-err').text(data.message);
                    }
                }
            });

        }
    });

    /**
     * This method is to validate the form fields which have req-cntrl class
     * @param $form
     */
    function validateForm($form) {
        var hasErr = false;
        var $fields = $form.find('.req-cntrl');
        for(var i=0; i<$fields.length; i++) {
            var $currentFld = $($fields[i]);
            var $reqErr = $currentFld.parent().find('.pure-form-message-inline');
            if(!$currentFld.val()) {
                hasErr = true;
                $reqErr.show();
            } else {
                $reqErr.hide();
            }
        }
        return hasErr;
    }

    // Click event for logout link
    $('body').on('click','.logout-link',function(){
        jQuery.ajax({
            type: "POST",
            url:  basuri+"/admin/logout",
            data: {}, // serializes the form's elements.
            success: function(data) {
                if(data.status===true)
                {
                    window.location.href = 'login';
                }
            }
        });
    });

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
                        dis.html('<i class="fa fa-ban"></i>');
                        dis.removeClass('button-success');
                        dis.addClass('button-warning');
                    }
                    else
                    {
                        dis.html('<i class="fa fa-check"></i>');
                        dis.removeClass('button-warning');
                        dis.addClass('button-success');
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
