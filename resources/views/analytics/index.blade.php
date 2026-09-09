<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Аналітика ліцензування | Укртрансбезпека</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
</head>
<body>
    <div class="app-shell">
        <header class="site-header">
            <div class="header-main">
                <a class="brand" href="{{ route('analytics.index') }}">
                    <img src="https://dsbt.gov.ua/templates/dsbt/images/logo/logo-white.svg" alt="Державна служба України з безпеки на транспорті">
                    <span>Державна служба України<br>з безпеки на транспорті</span>
                </a>
                <div class="header-tools">
                    <a href="#" aria-label="Пошук">⌕ <span>Пошук</span></a>
                    <a href="#" aria-label="English">EN</a>
                    <a href="#" aria-label="Налаштування доступності">◉ <span>Налаштування<br>доступності</span></a>
                </div>
                <button class="menu-button" type="button" aria-label="Відкрити меню">☰</button>
            </div>
            <nav class="topnav" aria-label="Головна навігація">
                <a href="#">Про Службу</a>
                <a href="#">Діяльність</a>
                <a href="#">Бізнесу</a>
                <a href="#">Реєстри</a>
                <a href="#">Громадянам</a>
                <a href="#">Новини</a>
                <a class="active" href="{{ route('analytics.index') }}">Аналітика</a>
                <a href="#">Контакти</a>
            </nav>
        </header>

        <section class="page-heading">
            <div>
                <div class="breadcrumbs"><a href="{{ route('analytics.index') }}">Головна</a><span>|</span><span>Аналітика</span></div>
                <p class="eyebrow">Дані за поточний період</p>
                <h1>Аналітика</h1>
                <h2 class="page-subtitle">Ліцензування автоперевізників по областях</h2>
                <p class="page-hint">Натисніть на область для детальної статистики</p>
            </div>
            <div class="summary-stats" aria-label="Загальна статистика">
                <div class="summary-stat issued"><strong>{{ number_format($totals['issued'], 0, ',', ' ') }}</strong><span>Видано за тиждень</span></div>
                <div class="summary-stat revoked"><strong>{{ number_format($totals['revoked'], 0, ',', ' ') }}</strong><span>Анульовано</span></div>
                <div class="summary-stat active"><strong>{{ number_format($totals['active'], 0, ',', ' ') }}</strong><span>Активних загалом</span></div>
            </div>
        </section>

        <main class="dashboard" id="license-dashboard">
            <section class="map-column">
                <div class="map-toolbar">
                    <div class="legend"><span>Менше</span><i></i><span>Більше ліцензій</span></div>
                    <div class="hover-readout" id="hover-readout" hidden></div>
                </div>
                <div class="map-card">
                    <svg id="license-map" viewBox="0 0 900 580" role="img" aria-label="Карта України з кількістю виданих ліцензій"></svg>
                    <div class="map-empty" id="map-empty" hidden>Геометрію карти не знайдено</div>
                </div>
                <div class="map-insights" aria-label="Підсумки карти">
                    <div class="map-insight"><span class="insight-icon">◌</span><div><strong>{{ $mapInsights['regions'] }}</strong><small>областей у вибірці</small></div></div>
                    <div class="map-insight"><span class="insight-icon">↗</span><div><strong>{{ $mapInsights['leadingRegion'] }}</strong><small>лідер за кількістю виданих</small></div></div>
                    <div class="map-insight"><span class="insight-icon">▣</span><div><strong>{{ $mapInsights['leadingIssued'] }}</strong><small>ліцензій у лідера за тиждень</small></div></div>
                </div>
            </section>

            <aside class="detail-panel" id="detail-panel" aria-hidden="true">
                <div class="detail-header">
                    <div><p>Регіон</p><h2 id="detail-name"></h2><span>Аналітика ліцензування</span></div>
                    <button class="icon-button" id="close-panel" type="button" aria-label="Закрити панель">×</button>
                </div>
                <div class="detail-body">
                    <div class="popup-mode-switcher" role="tablist" aria-label="Тип аналітики області">
                        <button class="popup-mode-button active" type="button" role="tab" aria-selected="true" data-popup-mode="licensing">Ліцензування</button>
                        <button class="popup-mode-button" type="button" role="tab" aria-selected="false" data-popup-mode="permits">Дозволи</button>
                        <button class="popup-mode-button" type="button" role="tab" aria-selected="false" data-popup-mode="irregular">Нерегулярні</button>
                    </div>
                    <section class="popup-view" id="popup-license-view">
                        <div class="trend-pill" id="detail-trend"></div>
                        <div class="metric-grid" id="detail-metrics"></div>
                        <div class="mini-charts">
                            <div><h3>Видано за 8 тижнів</h3><div class="bars" id="issued-bars"></div></div>
                            <div><h3>Анульовано за 8 тижнів</h3><div class="bars revoked-bars" id="revoked-bars"></div></div>
                        </div>
                        <div class="ratio-card"><p>Співвідношення</p><div class="ratio-row"><span>Видано</span><i><b id="issued-ratio"></b></i><strong id="issued-percent"></strong></div><div class="ratio-row"><span>Анульовано</span><i><b id="revoked-ratio"></b></i><strong id="revoked-percent"></strong></div></div>
                    </section>
                    <section class="popup-section permit-popup-section popup-view is-hidden" id="popup-permits-view">
                        <div class="popup-section-heading"><p>Дозволи</p><span>аналітика регіону</span></div>
                        <label class="permit-type-filter"><span>Тип дозволу</span><select id="popup-permit-type"><option value="all">Усі типи</option><option value="cargo">Вантажний</option><option value="bus">Автобусний</option></select></label>
                        <div class="metric-grid permit-metrics">
                            <div class="metric"><label>Перевізники</label><strong id="popup-carriers"></strong><small>з активними дозволами</small></div>
                            <div class="metric"><label>Пункти перетину</label><strong id="popup-border-points"></strong><small>доступні для оформлення</small></div>
                            <div class="metric"><label>Заявки ЄКМТ</label><strong id="popup-ekmt-applications"></strong><small>за обраний період</small></div>
                        </div>
                        <div class="ratio-card permit-type-summary"><p>Тип дозволу</p><div id="popup-permit-types"></div></div>
                    </section>
                    <section class="popup-section irregular-popup-section popup-view is-hidden" id="popup-irregular-view">
                        <div class="popup-section-heading"><p>Нерегулярні перевезення</p><span>за напрямами</span></div>
                        <div class="irregular-summary" id="popup-irregular"></div>
                    <section class="purpose-popup-section">
                        <div class="popup-section-heading"><p>Мета перевезення</p><span>заявки</span></div>
                        <div class="purpose-summary" id="popup-purposes"></div>
                    </section>
                    </section>
                    <p class="updated">Оновлено: {{ $updatedAt }}</p>
                </div>
            </aside>
        </main>

        <footer class="site-footer">
            <div class="footer-main">
                <a class="footer-brand" href="https://dsbt.gov.ua/" target="_blank" rel="noreferrer">
                    <img src="https://dsbt.gov.ua/templates/dsbt/images/logo/logo-white.svg" alt="Державна служба України з безпеки на транспорті">
                    <span>Державна служба України<br>з безпеки на транспорті<small>Офіційний веб-сайт</small></span>
                </a>
                <div class="footer-socials" aria-label="Соціальні мережі">
                    <a href="https://www.youtube.com/@Ukrtransbezpeka" target="_blank" rel="noreferrer">YT</a>
                    <a href="https://whatsapp.com/channel/0029VaiH665BVJkupkgH6U26" target="_blank" rel="noreferrer">WA</a>
                    <a href="https://t.me/ukrtransbezpeka" target="_blank" rel="noreferrer">TG</a>
                    <a href="https://www.facebook.com/DSBT.UA" target="_blank" rel="noreferrer">f</a>
                    <a href="https://t.me/UkrTransBezpekaBot" target="_blank" rel="noreferrer">⌁</a>
                </div>
                <div class="footer-contacts">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=50.43164561820379,30.51401432876819" target="_blank" rel="noreferrer">Поштова адреса: вул. Антоновича, 51, м. Київ, 03150</a>
                    <a href="mailto:contact@dsbt.gov.ua">Офіційна електронна пошта: contact@dsbt.gov.ua</a>
                    <a href="tel:+380443504304">Гаряча лінія Укртрансбезпеки: +38 (044) 350-43-04</a>
                    <span>Щоденно з 08:00 до 19:00</span>
                    <div><a href="https://old.dsbt.gov.ua/" target="_blank" rel="noreferrer">Попередня версія сайту</a><a href="https://dsbt.gov.ua/mapa-saitu" target="_blank" rel="noreferrer">Мапа сайту</a></div>
                </div>
            </div>
            <div class="footer-description">
                <span class="footer-emblem">✦</span>
                <p>Державна служба України з безпеки на транспорті (Укртрансбезпека) є центральним органом виконавчої влади, діяльність якого спрямовується і координується Кабінетом Міністрів України через Віцепрем’єр-міністра з відновлення України — Міністра розвитку громад та територій України.</p>
            </div>
            <div class="footer-bottom"><span>© {{ date('Y') }} Весь контент доступний за ліцензією</span><span>Creative Commons Attribution 4.0 International license, якщо не зазначено інше</span></div>
        </footer>
    </div>

    <script>
        window.analyticsData = @json($regions, JSON_UNESCAPED_UNICODE);
        window.analyticsMap = @json($map, JSON_UNESCAPED_UNICODE);
        window.permitAnalytics = @json($permitAnalytics, JSON_UNESCAPED_UNICODE);
    </script>
    <script src="{{ asset('js/analytics.js') }}"></script>
</body>
</html>
