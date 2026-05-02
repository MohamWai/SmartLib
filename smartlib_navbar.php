<?php
declare(strict_types=1);

function smartlib_navbar(string $active): void
{
    $items = [
        ['slug' => 'home', 'href' => 'index.html', 'label' => 'Home'],
        ['slug' => 'search', 'href' => 'search.php', 'label' => 'Search'],
        ['slug' => 'reports', 'href' => 'reports.php', 'label' => 'Reports'],
        ['slug' => 'about', 'href' => 'about.html', 'label' => 'About'],
        ['slug' => 'contact', 'href' => 'contact.php', 'label' => 'Contact Us'],
        ['slug' => 'signup', 'href' => 'signup.html', 'label' => 'Sign up'],
        ['slug' => 'account', 'href' => 'account.php', 'label' => 'Account'],
        ['slug' => 'admin', 'href' => 'admin.php', 'label' => 'Admin'],
        ['slug' => 'questionnaire', 'href' => 'questionaire.html', 'label' => 'Questionnaire'],
        ['slug' => 'calculator', 'href' => 'calculator.html', 'label' => 'Calculator'],
        ['slug' => 'fun', 'href' => 'fun.html', 'label' => 'Fun'],
    ];
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-style shadow-sm" style="background-color: rgb(0, 54, 82);">
      <div class="container-fluid px-3 px-lg-4">
        <a href="index.html" class="navbar-brand d-flex align-items-center gap-2 text-white text-decoration-none">
          <img
            src="./assets/SmartLib_logo.jpg"
            alt="SmartLib logo"
            width="40"
            height="30"
            class="d-inline-block align-text-top"
          />
          <span class="fw-semibold">SmartLib</span>
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#smartlibNavbar"
          aria-controls="smartlibNavbar"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="smartlibNavbar">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-2 flex-lg-row flex-lg-nowrap align-items-lg-center">
            <?php foreach ($items as $item) :
                $isActive = $item['slug'] === $active;
                $cls = 'nav-link' . ($isActive ? ' active' : '');
                $aria = $isActive ? ' aria-current="page"' : '';
                ?>
            <li class="nav-item">
              <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="<?= $cls ?>"<?= $aria ?>><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </nav>
    <?php
}
