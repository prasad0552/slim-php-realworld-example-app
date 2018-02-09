$(document).ready(function(){


            $("#promotion_data").validate({
                rules: {
                    name: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    promo: {
                        required: true
                    },
                    start_date: {
                        required: true,
                        date: true
                    },
                    end_date: {
                        required: true,
                        date: true
                    },
                    percent_off: {
                        required: function(element) {
                            if ($('#percentage_discount').val=='percentage_discount') {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        number: true
                    },
                    fixed_off: {
                        required: function(element) {
                            if ($('#fixed_field').val=='fixed_amount') {
                                return false;
                            } else {
                                return true;
                            }
                        },
                        number: true
                    }
                },
                messages: {
                    name: {
                        required: "Name is required"
                    },
                    type: {
                        required: "Type is required"
                    },
                    promo: {
                        required: "Promo is required"
                    },
                    start_date: {
                        required: "Start Date is required",

                    },
                    end_date: {
                        required: "End Date is required",

                    },
                    percent_off: {
                        required: "Percent is required",

                    },
                    fixed_off: {
                        required: "Amount is required",

                    }

                },
            });


    $( "#promotion_start_date,#promotion_end_date" ).datepicker({
        changeYear: true
    });

    $('#promo_id').change(function(){
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





        $("#promotion_data_btn").click(function (event) {


            var val=$("#promotion_data").valid();

                if( val!=false) {

                jQuery.ajax({
                    type: "POST",
                    url: basuri + "/admin/discount",
                    data: $("#promotion_data").serialize(), // serializes the form's elements.
                    success: function (data) {
                        console.log(data); // show response from the php script. (use the developer toolbar console, firefox firebug or chrome inspector console)
                        location.href = basuri + '/admin/discounts';
                    }
                });
            }
            else
                {

                    return false;
                }

        });


});