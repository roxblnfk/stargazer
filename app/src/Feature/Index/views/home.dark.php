<extends:layout title="[[Stargazer - Open Source Stars Analytics]]"/>

<stack:push name="styles">
<style>
    :root {
        --bg-primary: #0a0a0f;
        --bg-secondary: #111827;
        --accent-purple: #8b5cf6;
        --accent-blue: #3b82f6;
        --accent-cyan: #06b6d4;
        --text-white: #ffffff;
        --text-gray-100: #f3f4f6;
        --text-gray-400: #9ca3af;
        --text-gray-500: #6b7280;
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --shadow-glow: rgba(139, 92, 246, 0.3);
    }
</style>
<link href="/styles/welcome.css" rel="stylesheet" type="text/css" />
</stack:push>

<define:body>
    <div class="min-vh-100 cosmic-bg position-relative">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <!-- Main Card -->
                    <div class="main-card p-4 p-md-5">
                        <!-- Header with floating stars -->
                        <div class="text-center position-relative">
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <i class="bi bi-star-fill star-decoration star-left"></i>
                                <h1 class="hero-title mb-0">Stargazer</h1>
                                <i class="bi bi-star-fill star-decoration star-right"></i>
                            </div>

                            <p class="hero-description">
                                Мы создаем открытый исходный код и очень рассчитываем на ваши звезды!
                                Оцените наши проекты, а за это мы подарим вам мерч.
                            </p>
                        </div>

                        <!-- Modern Feature Cards -->
                        <div class="feature-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-github"></i>
                                </div>
                                <h3 class="feature-title">Open Source</h3>
                                <p class="feature-desc">Поддерживаем открытые проекты и сообщество</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                                <h3 class="feature-title">Звезды важны</h3>
                                <p class="feature-desc">Каждая звезда помогает проектам расти</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-gift"></i>
                                </div>
                                <h3 class="feature-title">Мерч в подарок</h3>
                                <p class="feature-desc">Получайте призы за поддержку проектов</p>
                            </div>
                        </div>

                        <!-- CTA Section -->
                        <div class="cta-section">
                            <h2 class="cta-title">Начните прямо сейчас!</h2>
                            <p class="cta-subtitle">Введите свой GitHub username и узнайте, какие проекты вы поддержали</p>

                            <form class="modern-form">
                                <div class="input-wrapper">
                                    <input type="text"
                                           class="modern-input"
                                           placeholder="ваш-github-username">
                                </div>
                                <button class="cta-button" type="submit">
                                    Поехали!
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</define:body>
