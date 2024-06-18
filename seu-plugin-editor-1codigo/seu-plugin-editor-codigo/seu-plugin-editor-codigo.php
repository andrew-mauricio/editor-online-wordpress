<?php
/**
 * Plugin Name: Seu Plugin Editor de Código
 * Description: Um plugin para adicionar um editor de código no front-end usando um shortcode.
 * Version: 1.0
 * Author: Seu Nome
 */

// Se não definido, sai sem fazer nada
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Inclui as classes do plugin
require_once plugin_dir_path( __FILE__ ) . 'includes/class-seu-plugin-editor-codigo.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-seu-plugin-editor-codigo-db.php';

// Registra as funções de ativação e desativação
register_activation_hook( __FILE__, array( 'Seu_Plugin_Editor_Codigo_DB', 'ativar' ) );
register_deactivation_hook( __FILE__, array( 'Seu_Plugin_Editor_Codigo_DB', 'desativar' ) );

// Inicializa o plugin
function run_seu_plugin() {
	$plugin = new Seu_Plugin_Editor_Codigo();
	$plugin->run();
}
run_seu_plugin();