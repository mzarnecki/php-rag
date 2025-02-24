$(function(){
    $("form").submit(function(e){
        var $form = $(this);
        $.ajax({
            type: "POST",
            data: {'prompt': $form.find('textarea').val()},
            url: $form.attr('action'),
            success: function (data) {
                $("#response .data").html('<h2>Response</h2>' + data.response + '<br/><br/><br/><h2>Retrieved documents</h2>' + data.documents.join('<br/>'));
                $("#response .spinner").hide();
            },
            dataType: 'JSON',
            beforeSend: function () {
                $("#response .data").html('');
                $("#response .spinner").show();
            }
        });
        return false;
    });
});
