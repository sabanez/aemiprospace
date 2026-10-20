<?php get_header(); ?>
    <main>
        <section class="mainSection">
            <div class="sectionHeader">
                <h2>WikiMi</h2>
                <!--            <p>-->
                <!--                Obtenga acceso a los artículos premium que lo ayudarán a brindar un mejor servicio a sus pacientes. Puedes filtrarlos basado en diferentes categorías como dermatología, enfermedades respiratorias, etc.-->
                <!--            </p>-->
                <p>
                    Aquí encontrarás todos los recursos disponibles en microinmunoterapia. Puedes
                    filtrarlos por categorías o por formatos. </p>
                <p>
                    Recuerda que podrás desbloquear más materiales exclusivos haciéndote soci@ de
                    AEMI. </p>
            </div>
            <div class="sectionContainer">
                <form class="filterResults wikimiCard card" id="wikimifiltrarResultados" action="<?php echo get_category_link($cat); ?>">
                    <div class="filterCard">
                        <div class="filterName flex">
                            <h4>Filtrar Resultados</h4>
                            <span class="tooltip"><img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/not-find.png'); ?>" alt="message">
                                <!--                            <span class="tooltiptext">Restaurar el sistema inmunológico en el intestino Enfermedades</span>-->
                            <span class="tooltiptext">Selecciona los filtros que más te interesan para acotar tu búsqueda.</span>
                        </span>
                        </div>
                        <div class="filterSearch">
                            <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/wikiser.png'); ?>" alt="search-icon">
                            <input class="fullWidth" type="text" name="search" placeholder="Buscar por nombre..." value="<?php echo isset($_GET['search']) && $_GET['search'] ? esc_attr(sanitize_text_field($_GET['search'])) : ''; ?>">
                        </div>
                        <?php
                        /*<div class="checkFre flex">
                            <label class="customLabel">Gratis
                                <input type="checkbox" value="free" name="content_type_plan[]" <?php echo isset($_GET['content_type_plan']) && $_GET['content_type_plan'] && is_array($_GET['content_type_plan']) && in_array('free', $_GET['content_type_plan']) ? 'checked="checked"' : ''; ?>>
                                <span class="checkmark"></span>
                            </label>
                            <label class="customLabel">Socio (Pagado)
                                <input type="checkbox" value="socio" name="content_type_plan[]" <?php echo isset($_GET['content_type_plan']) && $_GET['content_type_plan'] && is_array($_GET['content_type_plan']) && in_array('socio', $_GET['content_type_plan']) ? 'checked="checked"' : ''; ?>>
                                <span class="checkmark"></span>
                            </label>
                        </div> */ ?>
                    </div>
                    <div class="filterCard">
    <div class="filterName">
        <h4>Categoría</h4>
    </div>

    <?php 
    // Aquí ajustamos la llamada para que sólo incluya las categorías de área terapéutica
    $categories = get_categories([
        'taxonomy' => 'category', 
		'exclude' => [1], // Excluir las categorías con estos term_id
    ]); 
    ?>

    <?php $categories_show_more = ($categories && count($categories) > 5 ? true : false); ?>
    <?php
    $category_temp = [];
    if (isset($_GET['category']) && $_GET['category']) {
        $category_temp = AEMIWikimiPageGeneralActionsAndFunctions::resolveTaxonomyValues($_GET['category'], 'category');
    } elseif (!isset($args_news['tax_query']) && !isset($args_news['content_type_plan']) && !isset($args_news['content_type']) && !isset($args_news['search']) && $cat) {
        $category_temp = AEMIWikimiPageGeneralActionsAndFunctions::resolveTaxonomyValues($cat, 'category');
    }

    $content_type_temp = isset($_GET['content_type']) && $_GET['content_type'] ? AEMIWikimiPageGeneralActionsAndFunctions::resolveTaxonomyValues($_GET['content_type'], 'content_type') : [];
    $content_level_temp = isset($_GET['content_level']) && $_GET['content_level'] ? AEMIWikimiPageGeneralActionsAndFunctions::resolveTaxonomyValues($_GET['content_level'], 'content_level') : [];
    ?>
    <input id="category--buttons-show-more" type="checkbox" class="enabled-buttons-show-more no-auto-submit"/>
    <div class="selectLabels <?php echo $categories_show_more ? 'buttons-show-more' : ''; ?>">
        <label class="customLabel">Todas las categorías
            <input type="checkbox" value="1" name="category[]" <?php echo in_array(1, $category_temp) ? 'checked="checked"' : ''; ?>>
            <span class="checkmark"></span>
        </label>
        <?php if ($categories) { ?>
            <?php foreach ($categories as $category) { ?>
                <label class="customLabel"><?php echo $category->name; ?>
                    <input type="checkbox" value="<?php echo $category->term_id; ?>" name="category[]" <?php echo in_array($category->term_id, $category_temp) ? 'checked="checked"' : ''; ?> />
                    <span class="checkmark"></span>
                </label>
            <?php } ?>

            <?php if ($categories_show_more) { ?>
                <label id="show-more-category" for="category--buttons-show-more">+ <?php echo count($categories) - 5; ?> más</label>
            <?php } ?>
        <?php } ?>
    </div>
</div>

                    <?php $categories_doc = get_categories(['taxonomy' => 'content_type']); ?>
                    <div class="filterCard">
                        <div class="filterName">
                            <h4>Formato</h4>
                        </div>
                        <div class="selectLabels">
                            <?php if ($categories_doc) { ?><?php foreach ($categories_doc as $category) { ?>
                                <label class="customLabel"><?php echo $category->name; ?>
                                    <input type="checkbox" value="<?php echo $category->term_id; ?>" name="content_type[]" <?php echo in_array($category->term_id, $content_type_temp) ? 'checked="checked"' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            <?php } ?><?php } ?>
                        </div>
                    </div>
                    <?php $levels = get_categories(['taxonomy' => 'content_level']); ?>
                    <div class="filterCard">
                        <div class="filterName">
                            <h4>Nivel</h4>
                        </div>
                        <div class="selectLabels">
                            <?php if ($levels) { ?><?php foreach ($levels as $level) { ?>
                                <label class="customLabel"><?php echo $level->name; ?>
                                    <input type="checkbox" value="<?php echo $level->term_id; ?>" name="content_level[]" <?php echo in_array($level->term_id, $content_level_temp) ? 'checked="checked"' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            <?php } ?><?php } ?>
                        </div>
                    </div>
                </form>
                <div class="rightContent" id="show-results-wikimi">
                    <div class="mainCard">
                        <div class="cardFlex flex">
                            <?php
                            $returnInfoData = AEMIWikimiPageGeneralActionsAndFunctions::getContentPage(isset($_GET) ? $_GET : [], $cat);

                            echo $returnInfoData['pageContent'];
                            ?>
                        </div>
                        <div class="rightPagination"><?php echo $returnInfoData['pagePagination']; ?></div>
                    </div>
                    <div class="loading-element-inner"></div>
                </div>
            </div>


        </section>
    </main>

    <script>
        jQuery(document).ready(function ($) {
            let currentPagination = 1;

            function aemi_process_request_content_page() {
                var thisRequest = jQuery(document.getElementById('wikimifiltrarResultados'));
                document.getElementById('show-results-wikimi').classList.add('loading-element');

                var formData = $(thisRequest).serialize();

                var formDataJSON = {
                    content_type_plan: [],
                    content_type: [],
                    category: [],
                    content_level: []
                };

                $('input[name="content_type_plan[]"]:checked').each(function () {
                    var checkboxValue = $(this).val();
                    formDataJSON.content_type_plan.push(checkboxValue);
                });

                $('input[name="content_type[]"]:checked').each(function () {
                    var checkboxValue = $(this).val();
                    formDataJSON.content_type.push(checkboxValue);
                });

                $('input[name="content_level[]"]:checked').each(function () {
                    var checkboxValue = $(this).val();
                    formDataJSON.content_level.push(checkboxValue);
                });

                $('input[name="category[]"]:checked').each(function () {
                    var checkboxValue = $(this).val();
                    formDataJSON.category.push(checkboxValue);
                });

                if ($('input[name="search"]') && $('input[name="search"]').val()) {
                    formDataJSON['search'] = $('input[name="search"]').val() || '';
                }

                formDataJSON.action = 'aemi_get_content_page';
                formDataJSON.baseUrlPagination = '<?php echo get_category_link($cat); ?>';
                formDataJSON.pagination = currentPagination;

                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: formDataJSON,
                    success: function (response) {
                        document.getElementById('show-results-wikimi').classList.remove('loading-element');

                        if (response && response.data && response.data.pageContent) {
                            $('#show-results-wikimi .cardFlex').html(response.data.pageContent);
                            $('#show-results-wikimi .rightPagination').html(response.data.pagePagination);
                        }
                    }
                });
            }

            let timerId;

            const formInputs = document.querySelectorAll('#wikimifiltrarResultados input:not(.no-auto-submit)');
            formInputs.forEach(input => {
                input.addEventListener('input', () => {
                    clearTimeout(timerId);

                    document.getElementById('show-results-wikimi').classList.add('loading-element');
                    currentPagination = 1;

                    timerId = setTimeout(() => {
                        aemi_process_request_content_page();
                    }, 3000);
                });
            });

            $('#wikimifiltrarResultados').submit(function (e) {
                e.preventDefault();
                aemi_process_request_content_page(this);
            });

            jQuery('body').on('click', '.rightPagination .paginationHolder a.paginate', function (e) {
                e.preventDefault();
                var hrefLink = $(this).prop('href');
                var matchPagination = hrefLink.match(/\/page\/(\d+)/);
                currentPagination = 1;

                if (matchPagination && matchPagination[1]) {
                    currentPagination = matchPagination[1];
                }

                aemi_process_request_content_page();
            });
        });
    </script>
<?php get_footer(); ?>