<?php

class Seu_Plugin_Editor_Codigo {

    public function run() {
        add_shortcode('editor-codigo', array($this, 'render_editor'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_editor_scripts'));
        add_action('wp_ajax_save_user_code', array($this, 'save_user_code'));
        add_action('wp_ajax_retrieve_user_code', array($this, 'retrieve_user_code'));
    }

    public function render_editor($atts) {
        ob_start();
        ?>
        <div id="editor-container">
            <div id="editor-tabs" class="left-column">
                <div class="tabs">
                    <button class="tab-button" data-tab="html-editor">HTML</button>
                    <button class="tab-button" data-tab="css-editor">CSS</button>
                    <button class="tab-button" data-tab="js-editor">JavaScript</button>
                </div>
                <div id="html-editor" class="editor"></div>
                <div id="css-editor" class="editor" style="display:none;"></div>
                <div id="js-editor" class="editor" style="display:none;"></div>
                <input type="text" id="code-name" placeholder="Nome do Código">
                <button id="save-code-button">Salvar Código</button>
                <button id="retrieve-code-button">Recuperar Código</button>
            </div>
            <div id="preview-container" class="right-column">
                <iframe id="preview-iframe"></iframe>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function enqueue_editor_scripts() {
        wp_enqueue_script('ace-editor', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js', array(), null, true);
        wp_enqueue_script('editor-codigo-js', plugin_dir_url(__FILE__) . '../js/ajax-editor-codigo.js', array('jquery', 'ace-editor'), null, true);
        wp_localize_script('editor-codigo-js', 'ajax_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('editor-codigo-nonce')
        ));
        wp_enqueue_style('seu-plugin-estilo-editor', plugin_dir_url(__FILE__) . '../css/estilo-editor-codigo.css');
    }

    public function save_user_code() {
        check_ajax_referer('editor-codigo-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Você precisa estar logado para salvar o código.');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'seu_plugin_codigos';

        $user_id = get_current_user_id();
        $code_name = sanitize_text_field($_POST['code_name']);
        $html_code = wp_kses_post($_POST['html_code']);
        $css_code = wp_strip_all_tags($_POST['css_code']);
        $js_code = wp_strip_all_tags($_POST['js_code']);
        $created_at = current_time('mysql');
        $updated_at = current_time('mysql');

        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'code_name' => $code_name,
                'html_code' => $html_code,
                'css_code' => $css_code,
                'js_code' => $js_code,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );

        wp_send_json_success('Código salvo com sucesso.');
    }

    public function retrieve_user_code() {
        check_ajax_referer('editor-codigo-nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Você precisa estar logado para recuperar o código.');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'seu_plugin_codigos';

        $user_id = get_current_user_id();
        $code_name = sanitize_text_field($_POST['code_name']);

        $code = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT html_code, css_code, js_code FROM $table_name WHERE user_id = %d AND code_name = %s",
                $user_id,
                $code_name
            ),
            ARRAY_A
        );

        if ($code) {
            wp_send_json_success($code);
        } else {
            wp_send_json_error('Código não encontrado.');
        }
    }
}
