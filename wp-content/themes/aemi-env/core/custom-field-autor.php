<?php
class AEMI_Custom_Author_Name_Metabox
{
    public function __construct()
    {
        // Adiciona o metabox no painel de edição de postagens
        add_action('add_meta_boxes', [$this, 'add_metabox']);

        // Salva o valor do campo personalizado
        add_action('save_post', [$this, 'save_custom_author_name']);
    }

    // Adiciona o metabox
    public function add_metabox()
    {
        add_meta_box(
            'aemi_custom_author_name_metabox', // ID único do metabox
            'Nombre de autor:', // Título do metabox
            [$this, 'metabox_callback'], // Função de callback para renderizar o conteúdo do metabox
            'post', // Tipo de postagem em que o metabox deve aparecer (pode ser ajustado para outras postagens)
            'side', // Contexto (pode ser 'normal', 'advanced', ou 'side')
            'high' // Prioridade (pode ser 'high', 'core', 'default' ou 'low')
        );
    }

    // Callback para renderizar o conteúdo do metabox
    public function metabox_callback($post)
    {
        // Recupera o valor atual do campo
        $author_name = get_post_meta($post->ID, '__aemi_custom_author_name', true);

        // Renderiza o campo de entrada
        ?>
        <br/>
        <input type="text" id="aemi_custom_author_name" name="aemi_custom_author_name" value="<?php echo esc_attr($author_name); ?>" style="width: 100%;">
        <?php
    }

    // Salva o valor do campo personalizado
    public function save_custom_author_name($post_id)
    {
        if (isset($_POST['aemi_custom_author_name'])) {
            update_post_meta($post_id, '__aemi_custom_author_name', sanitize_text_field($_POST['aemi_custom_author_name']));
        }
    }
}

// Instancia a classe
new AEMI_Custom_Author_Name_Metabox();

function get_custom_author_name_by_post_id($post_id)
{
    // Obtém o valor atual do campo
    $author_name = get_post_meta($post_id, '__aemi_custom_author_name', true);

    // Retorna o valor do campo
    return $author_name;
}
