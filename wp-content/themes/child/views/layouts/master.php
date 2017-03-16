<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $this->e(bloginfo('name')) ?> - <?= $this->v('title', function(){ return bloginfo('description'); }) ?></title>
        <link rel="stylesheet" href="<?= get_stylesheet_directory_uri() ?>/assets/dist/styles.c0f1f8b2822e41e38268df856850ff8d.css">
        <?php wp_head(); ?>
    </head>
    <body>
        <div class="container">

            <header><h1>Fructify</h1><hr></header>

            <main><?= $this->supply('main') ?></main>

            <footer>
                <h6>Built By <a href="https://github.com/brad-jones/">Brad</a></h6>
            </footer>

        </div>
        <script src="<?= get_stylesheet_directory_uri() ?>/assets/dist/script.209799dd7657fac8d8efaf16f62ebfa7.js"></script>
        <?php wp_footer(); ?>
    </body>
</html>
