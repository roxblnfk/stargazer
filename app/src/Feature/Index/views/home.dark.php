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
    <div class="vh-100 cosmic-bg position-relative d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <!-- Main Card -->
                    <div class="main-card p-4">
                        <!-- Header with floating stars -->
                        <div class="text-center position-relative">
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <i class="bi bi-star-fill star-decoration star-left"></i>
                                <h1 class="hero-title mb-0">[[Stargazer]]</h1>
                                <i class="bi bi-star-fill star-decoration star-right"></i>
                            </div>

                            <p class="hero-description">
                                [[We create an open source code and really count on your stars!]]
                                [[Evaluate our projects, and for this we will give you a measure.]]
                            </p>
                        </div>

                        <!-- Modern Feature Cards -->
                        <div class="feature-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-github"></i>
                                </div>
                                <h3 class="feature-title">Open Source</h3>
                                <p class="feature-desc">[[We support open projects and community]]</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-star"></i>
                                </div>
                                <h3 class="feature-title">[[Stars are important]]</h3>
                                <p class="feature-desc">[[Each star helps projects to grow]]</p>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-gift"></i>
                                </div>
                                <h3 class="feature-title">[[Merch as a gift]]</h3>
                                <p class="feature-desc">[[Get prizes for project support]]</p>
                            </div>
                        </div>

                        <!-- CTA Section -->
                        <div class="cta-section">
                            <h2 class="cta-title">[[Start now!]]</h2>
                            <p class="cta-subtitle">[[Enter your GitHub username and see which projects you have backed]]</p>

                            <form class="modern-form">
                                <div class="input-wrapper">
                                    <input type="text"
                                           name="username"
                                           class="modern-input"
                                           placeholder="github-username">
                                </div>
                                <button class="cta-button" type="submit">
                                    [[Go!]]
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
