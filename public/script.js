$(function(){
    $("form").submit(function(e){
        var $form = $(this);
        $.ajax({
            type: "POST",
            data: {
                'prompt': $form.find('textarea').val(),
                'evaluate': $form.find('input[name="evaluate"]').is(':checked') ? '1' : '0'
            },
            url: $form.attr('action'),
            success: function (data) {
                $("#response .data").html(
                    '<h2>Response</h2>' + data.response
                    + '<br/><br/><br/><h2>Retrieved documents</h2>' + data.documents.join('<br/>')
                    + (data.evaluation ? ('<br/><br/><br/><h2>Evaluation</h2>' + JSON.stringify(data.evaluation)) : '')
                );
                $("#response .spinner").hide();
            },
            dataType: 'JSON',
            beforeSend: function () {
                $("#response .data").html('');
                $("#response .spinner").show();
            },
            timeout: 300000,
        });
        return false;
    });
});
