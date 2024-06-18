jQuery(document).ready(function ($) {
    var htmlEditor = ace.edit("html-editor");
    htmlEditor.session.setMode("ace/mode/html");
    var cssEditor = ace.edit("css-editor");
    cssEditor.session.setMode("ace/mode/css");
    var jsEditor = ace.edit("js-editor");
    jsEditor.session.setMode("ace/mode/javascript");

    function switchTab(tabId) {
        $('.editor').hide();
        $('#' + tabId).show();
    }

    $('.tab-button').on('click', function () {
        var tabId = $(this).data('tab');
        switchTab(tabId);
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
    });

    $('#save-code-button').on('click', function () {
        var data = {
            'action': 'save_user_code',
            'nonce': ajax_object.nonce,
            'code_name': $('#code-name').val(),
            'html_code': htmlEditor.getValue(),
            'css_code': cssEditor.getValue(),
            'js_code': jsEditor.getValue(),
        };

        $.post(ajax_object.ajaxurl, data, function (response) {
            if (response.success) {
                alert('Código salvo com sucesso!');
            } else {
                alert('Erro ao salvar o código: ' + response.data);
            }
        });
    });

    $('#retrieve-code-button').on('click', function () {
        var data = {
            'action': 'retrieve_user_code',
            'nonce': ajax_object.nonce,
            'code_name': $('#code-name').val(),
        };

        $.post(ajax_object.ajaxurl, data, function (response) {
            if (response.success) {
                var code = response.data;
                htmlEditor.setValue(code.html_code);
                cssEditor.setValue(code.css_code);
                jsEditor.setValue(code.js_code);
            } else {
                alert('Erro ao recuperar o código: ' + response.data);
            }
        });
    });

    function renderPreview() {
        var html = htmlEditor.getValue();
        var css = '<style>' + cssEditor.getValue() + '</style>';
        var js = '<script>' + jsEditor.getValue() + '</script>';

        var iframe = document.getElementById('preview-iframe');
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(html + css + js);
        iframeDoc.close();
    }

    htmlEditor.session.on('change', renderPreview);
    cssEditor.session.on('change', renderPreview);
    jsEditor.session.on('change', renderPreview);

    renderPreview();
});