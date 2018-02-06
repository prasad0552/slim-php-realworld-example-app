$(document).ready(function(){




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

});