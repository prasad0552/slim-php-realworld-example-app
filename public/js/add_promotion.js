$(document).ready(function(){


    var baseUrl = document.location.origin;

    $( "#promotion_start_date,#promotion_end_date" ).datepicker();

    $('#promotion_type').change(function(){
        val=$(this).val();
        if(val=="fixed_amount")
        {
            $("#amount_field").show();
            $("#percent_field").hide();
        }
        else if(val=="percentage_discount")
        {
            $("#amount_field").hide();
            $("#percent_field").show();
        }
        else
        {
            $("#percent_field,#amount_field").hide();
        }
    });

    $("#promotion_data").click(function(event){



        /*$.post(
            baseUrl+"/slim_mvc/public/admin/discount",

            function(data) {
                console.log(data);
            }
        );*/

        jQuery.ajax({
            type: "POST",
            url:  baseUrl+"/slim_mvc/public/admin/discount",
            data: $("#promotion_data").serialize(), // serializes the form's elements.
            success: function(data) {
                console.log(data); // show response from the php script. (use the developer toolbar console, firefox firebug or chrome inspector console)
            }
        });


    });

});