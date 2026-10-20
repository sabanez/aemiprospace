<?php

class AEMIWikimiPageGeneralActionsAndFunctions
{
    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        add_action('wp_ajax_aemi_get_content_page', [$this, 'aemi_get_content_page']);
        add_action('wp_ajax_nopriv_aemi_get_content_page', [$this, 'aemi_get_content_page']);
    }

    public function aemi_get_content_page()
    {
        $temp = AEMIWikimiPageGeneralActionsAndFunctions::getContentPage(isset($_POST) ? $_POST : [], isset($_POST['cat']) ? $_POST['cat'] : []);

        if ($temp) {
            wp_send_json_success($temp);
        }

        wp_send_json_success([
            'pageContent' => '',
            'pagePagination' => '',
            'debug' => isset($_POST['debug_wikimi']) && in_array(strtolower((string) $_POST['debug_wikimi']), ['1', 'true', 'on', 'yes'], true) ? [
                'raw_request' => $_POST,
                'message' => 'No se devolvió contenido desde el handler.',
            ] : [],
        ]);
        exit;
    }

    private static function isDebugEnabled($argsGet = [])
    {
        $debug_value = '';

        if (isset($argsGet['debug_wikimi'])) {
            $debug_value = $argsGet['debug_wikimi'];
        } elseif (isset($_GET['debug_wikimi'])) {
            $debug_value = $_GET['debug_wikimi'];
        } elseif (isset($_POST['debug_wikimi'])) {
            $debug_value = $_POST['debug_wikimi'];
        }

        if ($debug_value === '' || $debug_value === null) {
            return false;
        }

        if (is_bool($debug_value)) {
            return $debug_value;
        }

        return in_array(strtolower((string) $debug_value), ['1', 'true', 'on', 'yes'], true);
    }

    public static function resolveTaxonomyValues($raw_values, $taxonomy)
    {
        if (empty($raw_values)) {
            return [];
        }

        if (!is_array($raw_values)) {
            $raw_values = array_map('trim', explode(',', sanitize_text_field($raw_values)));
        } else {
            $raw_values = array_map(function ($value) {
                return sanitize_text_field($value);
            }, $raw_values);
        }

        $resolved_ids = [];

        foreach ($raw_values as $value) {
            if ($value === '') {
                continue;
            }

            if (is_numeric($value)) {
                $resolved_ids[] = (int) $value;
                continue;
            }

            $term = get_term_by('slug', $value, $taxonomy);

            if ($term && !is_wp_error($term)) {
                $resolved_ids[] = (int) $term->term_id;
            }
        }

        return array_values(array_unique($resolved_ids));
    }

    public static function getContentPage($argsGet = [], $defaultCat = '')
    {
        $current_page = max(1, isset($argsGet['pagination']) && $argsGet['pagination'] ? $argsGet['pagination'] : get_query_var('paged'));

        $dataReturn = [
            'pageContent' => '',
            'pagePagination' => '',
            'page' => sanitize_text_field($current_page),
        ];

        $debug_enabled = self::isDebugEnabled($argsGet);

        $args_news = [
            'posts_per_page' => 20,
            'order' => 'DESC',
            'paged' => sanitize_text_field($current_page),
        ];

            if (isset($argsGet['search']) && $argsGet['search']) {
                $args_news['s'] = sanitize_text_field($argsGet['search']);
            }

            if (isset($argsGet['category']) && $argsGet['category']) {
                $category_ids = self::resolveTaxonomyValues($argsGet['category'], 'category');

                if (!empty($category_ids)) {
                    $args_news['tax_query'][] = [
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $category_ids,
                        'operator' => 'IN',
                    ];
                }
            }

            if (isset($argsGet['content_type']) && $argsGet['content_type']) {
                $content_type_ids = self::resolveTaxonomyValues($argsGet['content_type'], 'content_type');

                if (!empty($content_type_ids)) {
                    $args_news['tax_query'][] = [
                        'taxonomy' => 'content_type',
                        'field' => 'term_id',
                        'terms' => $content_type_ids,
                        'operator' => 'IN',
                    ];
                }
            }

            if (isset($argsGet['content_level']) && $argsGet['content_level']) {
                $content_level_ids = self::resolveTaxonomyValues($argsGet['content_level'], 'content_level');

                if (!empty($content_level_ids)) {
                    $args_news['tax_query'][] = [
                        'taxonomy' => 'content_level',
                        'field' => 'term_id',
                        'terms' => $content_level_ids,
                        'operator' => 'IN',
                    ];
                }
            }

            if (isset($argsGet['content_type_plan']) && $argsGet['content_type_plan']) {
                if (is_array($argsGet['content_type_plan']) && count($argsGet['content_type_plan']) == 1) {
                    if (in_array('free', $argsGet['content_type_plan'])) {
                        $args_news['meta_query'][] = [
                            'key' => '_content_for_subscribers',
                            'value' => '',
                            'compare' => '=',
                        ];
                    } else {
                        $args_news['meta_query'][] = [
                            'key' => '_content_for_subscribers',
                            'value' => 1,
                        ];
                    }
                }
            }

            if (!isset($args_news['tax_query']) && !isset($args_news['content_type_plan']) && !isset($args_news['content_type']) && !isset($args_news['search']) && $defaultCat) {
                $default_cat_ids = self::resolveTaxonomyValues($defaultCat, 'category');

                if (!empty($default_cat_ids)) {
                    $args_news['tax_query'][] = [
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => $default_cat_ids,
                        'operator' => 'IN',
                    ];
                }
            }

            if ($debug_enabled) {
                $debug_info = [
                    'raw_request' => $argsGet,
                    'resolved_category_ids' => isset($argsGet['category']) ? self::resolveTaxonomyValues($argsGet['category'], 'category') : [],
                    'resolved_content_type_ids' => isset($argsGet['content_type']) ? self::resolveTaxonomyValues($argsGet['content_type'], 'content_type') : [],
                    'resolved_content_level_ids' => isset($argsGet['content_level']) ? self::resolveTaxonomyValues($argsGet['content_level'], 'content_level') : [],
                    'query_args' => $args_news,
                ];
            }

            $query_news = new WP_Query($args_news);
            $output = '';

            if ($query_news->have_posts()) {
                while ($query_news->have_posts()) {
                    $query_news->the_post();

                    $thumbnail = get_the_post_thumbnail($query_news->post->ID, 'thumbnail', ['class' => 'card-image-full', 'loading' => 'lazy']);
                    $thumbnail = $thumbnail ? $thumbnail : '<img width="305" height="205" src="'.get_template_directory_uri().'/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image card-image-full" />';
                    $favorite_button = favorite_button($query_news->post->ID);
                    $content_type = get_aemi_content_types_post($query_news->post->ID, 'content_type', false);
                    $content_for_subscribers = get_post_meta($query_news->post->ID, '_content_for_subscribers', true);
                    $verify_user_viewed_post = verify_user_viewed_post($query_news->post->ID, get_current_user_id());
                    $contains_video_youtube_or_vimeo = preg_match('/https?:\/\/(www\.)?(youtube\.com\/watch\?v=[A-Za-z0-9_\-]+|vimeo\.com\/[0-9]+)/', $query_news->post->post_content) ?: false;

                    $output .= '<div href="'.get_permalink($query_news->post->ID).'" class="cardLink">';
                    $output .= '<div class="resultsCard wikimiCard card flex alignNormal">';
                    $output .= '<div class="imageLeft">';
                    $output .= '<a href="'.get_permalink($query_news->post->ID).'">';
                    $output .= $thumbnail;
                    $output .= '<span>';
                    // $output .= '<button><svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="solar:play-bold" opacity="0.8"><path id="Vector" d="M15.1649 6.62461C15.5051 6.80552 15.7897 7.0756 15.9881 7.40589C16.1865 7.73618 16.2914 8.11424 16.2914 8.49957C16.2914 8.88489 16.1865 9.26295 15.9881 9.59324C15.7897 9.92353 15.5051 10.1936 15.1649 10.3745L6.0897 15.3095C4.62841 16.1042 2.8335 15.0701 2.8335 13.4352V3.56461C2.8335 1.92836 4.62841 0.8949 6.0897 1.68894L15.1649 6.62461Z" fill="var(--color-purple)"></path></g></svg>'.sanitize_text_field($content_type).'</button>';
                    $output .= '</span>';

                    if ($contains_video_youtube_or_vimeo) {
                        $output .= '<div class="iconOverlay"><div class="imageOverlay"><img class="play-icon" src="'.get_aemi_url_assets_imgs('new-layout-v1/icon/play-icon.png').'"></div></div>';
                    }

                    if ($verify_user_viewed_post) {
                        $output .= '<span class="viewed-page-msg"><h3>Visualizado</h3></span>';
                    }

                    if ($content_for_subscribers) {
                        $output .= '<span class="show-socio-msg"><img src="'.get_aemi_url_assets_imgs('new-layout-v1/icon/star3.png').'" alt="star" /><h3>SOCIO</h3></span>';
                    }

                    $output .= '</a>';
                    $output .= '</div>';
                    $output .= '<div class="resultsCardContent" class="imageLeft">';
                    $output .= '<h3>'.sanitize_text_field($query_news->post->post_title).'</h3>';
                    $output .= '<p>'.get_aemi_text_excerpt($query_news->post, 100).'</p>';

                    $output .= '<div class="readMoreBtn">';
                    $output .= '<button><span>Ver<img src="'.get_aemi_url_assets_imgs('new-layout-v1/icon/rightarow.png').'" alt="Ver"></span></button>';
                    $output .= '</div>';
                    $output .= '<a href="'.get_permalink($query_news->post->ID).'"></a>';
                    $output .= '<button class="heartIcon '.$favorite_button['class'].'" data-id="'.$favorite_button['data-id'].'">';
                    $output .= '<svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="mdi:heart-outline"><path id="Vector" d="M14.6207 22.4146L14.4998 22.5354L14.3669 22.4146C8.62734 17.2067 4.83317 13.7629 4.83317 10.2708C4.83317 7.85417 6.64567 6.04167 9.06234 6.04167C10.9232 6.04167 12.7357 7.25 13.3761 8.89333H15.6236C16.264 7.25 18.0765 6.04167 19.9373 6.04167C22.354 6.04167 24.1665 7.85417 24.1665 10.2708C24.1665 13.7629 20.3723 17.2067 14.6207 22.4146ZM19.9373 3.625C17.8348 3.625 15.8169 4.60375 14.4998 6.13833C13.1828 4.60375 11.1648 3.625 9.06234 3.625C5.34067 3.625 2.4165 6.53708 2.4165 10.2708C2.4165 14.8262 6.52484 18.56 12.7478 24.2029L14.4998 25.7979L16.2519 24.2029C22.4748 18.56 26.5832 14.8262 26.5832 10.2708C26.5832 6.53708 23.659 3.625 19.9373 3.625Z" fill="#FF3366"></path></g></svg>';
                    $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="66.357" height="60.883" viewBox="0 0 66.357 60.883"><g id="mdi:heart-outline" transform="translate(-2.417 -3.625)"><path id="Vector" d="M50.526,3.625a19.953,19.953,0,0,0-14.93,6.9,19.952,19.952,0,0,0-14.93-6.9A18.051,18.051,0,0,0,2.417,21.873c0,12.508,11.281,22.761,28.368,38.255l4.811,4.38,4.811-4.38C57.493,44.634,68.774,34.382,68.774,21.873A18.051,18.051,0,0,0,50.526,3.625Z" transform="translate(0 0)"/></g></svg>';
                    $output .= '</button>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</div>';
                }
            } else {
                $output .= '<div class="card" id="search-wikimi-no-result"><h2>No se encontraron resultados para su búsqueda</h2></div>';
            }

            $dataReturn['pageContent'] = $output;

            if ($debug_enabled) {
                $debug_info['found_posts'] = $query_news->found_posts;
                $debug_info['post_ids'] = wp_list_pluck($query_news->posts, 'ID');
                $dataReturn['debug'] = $debug_info;
                error_log('[AEMI Wikimi Debug] '.wp_json_encode($debug_info));
            }

            $total_pages = $query_news->max_num_pages;
            $pagination_links = aemi_pagination([
                'current' => $current_page,
                'total' => $total_pages,
                'base' => (isset($_POST['baseUrlPagination']) && $_POST['baseUrlPagination'] ? $_POST['baseUrlPagination'].'%_%' : (isset($defaultCat) && $defaultCat ? get_category_link($defaultCat).'%_%' : '%_%')),
            ]);

            if ($pagination_links) {
                $dataReturn['pagePagination'] = '<div class="searchResultsPage">'.strtr($pagination_links, ['class="paginationHolder"' => 'class="paginationHolder" style="margin-top: 0;"']).'</div>';
            }
            wp_reset_postdata();

        return $dataReturn;
    }
}

new AEMIWikimiPageGeneralActionsAndFunctions();
