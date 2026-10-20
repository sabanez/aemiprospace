<div class="sidebar-single-webinar sidebar-archive-webinar">

    <form method="GET">
        <fieldset class="filters search-webinar mb-5">
            <div class="d-flex">
                <input type="text" name="web" placeholder="<?= esc_html_e('Buscar webinar...', AEMI_TEXT_DOMAIN) ?>" value="<?=isset($_GET['web']) ? $_GET['web'] : '' ?>">
                <button type="search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </fieldset>

        <fieldset class="filters mb-5">
            <div class="select-div">
                <select name="" id="ordination">
                    <option value="" selected disabled><?= esc_html_e('Ordenação padrão', AEMI_TEXT_DOMAIN) ?></option>
                </select>
            </div>
        </fieldset>

        <div class="switch-options skills mb-4">
            <p class="title-skills"><?= esc_html_e('Estado', AEMI_TEXT_DOMAIN) ?></p>

            <fieldset>
                <label class="switch">
                    <input type="checkbox" value="1" name="registered" <?= (isset($_GET['registered']) && $_GET['registered'] == '1') ? 'checked' : '' ?> >
                    <span class="slider round"></span>
                    <span class="legend"><?= esc_html_e('Matriculado', AEMI_TEXT_DOMAIN) ?></span>
                </label>
            </fieldset>

            <fieldset>
                <label class="switch">
                    <input type="checkbox" value="1" name="no_registered" <?= (isset($_GET['no_registered']) && $_GET['no_registered'] == '1') ? 'checked' : '' ?> >
                    <span class="slider round"></span>
                    <span class="legend"><?= esc_html_e('No matriculado', AEMI_TEXT_DOMAIN) ?></span>
                </label>
            </fieldset>
        </div>

        <button type="submit" class="button bg-primary">Filtrar Webinars</button>
    </form>

    <div class="skills my-5">
        <p class="title-skills"><?= esc_html_e('Categorías', AEMI_TEXT_DOMAIN) ?></p>

        <?php
            $categories = get_terms([
                'taxonomy' => 'categories_webinar',
                'hide_empty' => false
            ]);

            if (! is_wp_error($categories) ):
                foreach ($categories as $cat): ?>

                <a class="d-block my-3 fw-bold" href="<?= get_term_link($cat->term_id) ?>"><?= $cat->name ?></a>

        <?php endforeach;
            endif;
        ?>
    </div>

    <div class="skills">
        <p class="title-skills"><?= esc_html_e('Orador', AEMI_TEXT_DOMAIN) ?></p>

        <?php
        $oradores = get_terms([
            'taxonomy' => 'oradores_webinar',
            'hide_empty' => false
        ]);

        if (! is_wp_error($oradores) ):
            foreach ($oradores as $orador): ?>

                <a class="d-block my-3 fw-bold" href="<?= get_term_link($orador->term_id) ?>"><?= $orador->name ?></a>

            <?php endforeach;
        endif;
        ?>
    </div>

</div>