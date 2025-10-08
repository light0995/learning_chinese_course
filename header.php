<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body>

  <div class="overlay"></div>

    <header class="header">
        <div class="header__wrapper">
    
            <?php the_custom_logo(); ?>
                
                        <?php 
                        wp_nav_menu( array(
                            'menu'              => '', // ID, имя или ярлык меню
                            'menu_class'        => 'header__menu-items', // класс элемента <ul>
                            'menu_id'           => '', // id элемента <ul>
                            'container'         => false, // тег контейнера или false, если контейнер не нужен
                            'container_class'   => '', // класс контейнера
                            'container_id'      => '', // id контейнера
                            'fallback_cb'       => 'wp_page_menu', // колбэк функция, если меню не существует
                            'before'            => '', // текст (или HTML) перед <a
                            'after'             => '', // текст после </a>
                            'link_before'       => '', // текст перед текстом ссылки
                            'link_after'        => '', // текст после текста ссылки
                            'echo'              => true, // вывести или вернуть
                            'depth'             => 0, // количество уровней вложенности
                            'walker'            => '', // объект Walker
                            'theme_location'    => 'header-menu', // область меню
                            'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                    'item_spacing'      => 'preserve',
                            ) );
                        
                        ?>


                    <div class="header__login">вход</div>
                </div>
            </header>

    <section class="main">
    <div class="hamburger__toggle">
        <div class="hamburger__toggle-line"></div>
        <div class="hamburger__toggle-line"></div>
        <div class="hamburger__toggle-line"></div>
    </div>

    <div class="hamburger">
        <div class="close__btn"><img src="./icons/close.svg" alt="close"></div>
        <div class="logo hamburger__logo"><a href="#"><img src="./icons/logo.png" alt=""></a></div>
        <div class="header__login hamburger__login">вход</div>
        
        <ul class="hamburger__menu-items">
            <li class="hamburger__menu-item"><a href="#product__cards">Курсы</a></li>
            <li class="hamburger__menu-item"><a href="#">О нас</a></li>
            <li class="hamburger__menu-item"><a href="#">Контакты</a></li>
        </ul>
        <div class="hamburger__social">
            <div class="subtitle hamburger__social-title">Мы в соц сетях:</div>
            <div class="hamburger__social-links">
                <div class="hamburger__social-link"><a href="https://www.instagram.com/shidak.akhmat/"  class="social__toggle"><img src="./icons/social/instagram.svg" alt="inst"></a></div>
                <div class="hamburger__social-link"><a href="https://t.me/shidak_akhmat"   class="social__toggle"><img src="./icons/social/telegram.svg" alt="telegram"></a></div>
            </div>
        </div>
    </div>

        
     <div class="sidepanel">
        <div class="social sidepanel__social">
            <div class="social__item social__instagram"><a href="https://www.instagram.com/shidak.akhmat/"  class="social__toggle"><img src="./icons/social/instagram.svg" alt="inst"></a></div>
            <div class="social__item social__telegram"><a href="https://t.me/shidak_akhmat"   class="social__toggle"><img src="./icons/social/telegram.svg" alt="telegram"></a></div>
        </div>
        <div class="sidepanel__line"></div>
        <div class="sidepanel__text">Социальные сети</div>
    </div>


     <div class="modal">
            <div class="modal__close"></div>
            <div class="modal__choose">
                <div class="modal__login-title modal__login-title-active">Вход</div>
                <div class="modal__register-title">Регистрация</div>
            </div>

            <div class="modal__login-wrapper modal__login-wrapper-active">
                <form class="modal__form modal__form-login">
                    <input required name="login" type="text" class="modal__input modal__input-login" placeholder="Введите ваш логин">
                    <input required name="password" type="password" class="modal__input modal__input-login" placeholder="Введите ваш пароль">
                    <button class="btn modal__btn">Вход</button>
                </form>
            </div>

            <div class="modal__registration modal__registration-wrapper">
                <form action="#" class="modal__form modal__form-registration">
                    <input required name="name" type="text" class="modal__input" placeholder="Введите ваше имя">
                    <input required name="login" type="text" class="modal__input" placeholder="Введите логин">
                    <input required name="email" type="text" class="modal__input" placeholder="Введите вашу почту">
                    <input required name="password" type="password" class="modal__input" placeholder="Введите пароль">
                    <input required name="password2" type="password" class="modal__input" placeholder="Введите пароль повторно">
                    <button class="btn modal__btn modal__btn-registration">Зарегистрироваться</button>
                </form>
            </div>


        </div>

