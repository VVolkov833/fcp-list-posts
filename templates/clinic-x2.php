<article class="post-<?php the_ID() ?> <?php echo get_post_type() ?> type-<?php echo get_post_type() ?> status-<?php echo get_post_status() ?> entry" itemscope="" itemtype="https://schema.org/CreativeWork">

    <a class="entry-link-cover" rel="bookmark" href="<?php the_permalink(); ?>" title="<?php the_title() ?>"></a>

    <header class="entry-header">
    <?php
        $photo = fct1_meta( 'entity-photo', '', '', true )[0];
        $backg = fct1_meta( 'entity-background', '', '', true )[0];
        if ( $photo || $backg ) {
    ?>
        <div class="entry-photo<?php echo !$photo ? ' entry-background' : '' ?>">
            <?php
                fct1_image_print(
                    'entity/' . get_the_ID() . '/' . ( !$photo ? $backg : $photo ),
                    [454, 210],
                    ['center', 'top'],
                    get_the_title()
                )
            ?>
        </div>
    <?php } ?>
        <h2 class="entry-title" itemprop="headline">
            <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
        </h2>
        <div class="entry-badges">
            <div class="entry-verified" title="<?php _e( 'Verified', 'fcpfo-ea' ) ?>"></div>
            <?php if ( fct1_meta( 'entity-featured' ) ) { ?>
            <div class="entry-featured" title="<?php _e( 'Featured', 'fcpfo-ea' ) ?>"></div>
            <?php } ?>
        </div>
    </header>
    <div class="entry-details">
        <?php if ( $ava = fct1_meta( 'entity-avatar', '', '', true )[0] ) { ?>
        <div class="entity-avatar">
            <?php fct1_image_print( 'entity/' . get_the_ID() . '/' . $ava, [74,74], 0, get_the_title() . ' ' . __( 'Icon', 'fcpfo-ea' ) ) ?>
        </div>
        <?php } ?>
        <div class="entity-about">
            <p>
                <?php echo fct1_meta( 'entity-specialty' ); echo fct1_meta( 'entity-geo-city', ' in ' ) ?>
            </p>
            <?php if ( method_exists( 'FCP_Comment_Rate', 'stars_total_print' ) ) { ?>
                <?php FCP_Comment_Rate::stars_total_print() ?>
            <?php } ?>
        </div>
    </div>

</article>