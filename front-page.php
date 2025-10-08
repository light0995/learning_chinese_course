<?php get_header(); ?>
    <div class="promo">
        <div class="container">
            <div class="promo__content">
                <div class="promo__left">
                    <h1 class="title promo__title">Изучай китайский <br>с носителем языка!</h1>
                    <div class="promo__description promo__description-wrapper">
                        <div class="promo__description-text">Мы подготовили курсы для всех уровней:</div>
                        <ul class="promo__description-levels">
                            <li class="promo__description-level">HSK 1 — начальный
                                <div class="promo__description-level-line"></div>
                            </li>
                            <li class="promo__description-level">HSK 2 — элементарный
                                <div class="promo__description-level-line"></div>
    
                            </li>
                            <li class="promo__description-level">HSK 3 — средний
                                <div class="promo__description-level-line"></div>
    
                            </li>
                            <li class="promo__description-level">HSK 4 — выше среднего
                                <div class="promo__description-level-line"></div>
    
                            </li>
                            <li class="promo__description-level">HSK 5 — продвинутый
                                <div class="promo__description-level-line"></div>
    
                            </li>
                            <li class="promo__description-level">HSK 6 — свободное владение 
                                <div class="promo__description-level-line"></div>
    
                            </li>
                        </ul>
                        <button class="btn promo__description-btn">начать обучение</button>
                    </div>
                </div>
    
               <div class="promo__img-wrapper"><img src="./img/Vicky.png" alt="promo__img" class="promo__img"></div>
    
            </div>

        


        </div>
    </div>
    </section>

   

    <section class="product__cards" id="product__cards">

    <div class="container">
            <h2 class="product__cards-title">
                Каталог курсов
            </h2>

            <div class="product__cards-items">
                <?php
                $query = new WP_Query(['post_type' => 'course']);
                if ($query->have_posts()):
                    while ($query->have_posts()): $query->the_post(); ?>
                        <div class="product__cards-item">
                            <h3 class="product__cards-item-title"><?php the_title(); ?></h3>
                            <div class="product__cards-item-descr">
                                <?php the_excerpt(); ?>
                            </div>
                            <div class="product__cards-item-details"><a href="<?php the_permalink(); ?>">Подробнее</a></div>
                            <button class="btn product__cards-item-btn">Купить</button>
                        </div>
                    <?php endwhile;
                endif;
                wp_reset_postdata();
                ?>




            </div>

    </div>


        </section>
<?php get_footer(); ?>

