<?php require_once __DIR__ . "/smartlib_navbar.php"; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SmartLib - Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  </head>
  <body>
    <?php smartlib_navbar('contact'); ?>

    <div class="container py-4">
      <?php if (!empty($_GET['sent'])): ?>
        <div class="alert alert-success">Thank you — your message was saved. We will get back to you soon.</div>
      <?php elseif (!empty($_GET['err'])): ?>
        <div class="alert alert-danger">Please fill in all fields with a valid email and try again.</div>
      <?php endif; ?>

      <div class="d-flex row justify-content-center justify-content-between align-items-center min-vh-50 py-3">
        <div class="col-lg mb-4 mb-lg-0">
          <h1>
            We’re Glad You’re
            <span style="color: rgb(50, 192, 116)">Reaching Out</span>
          </h1>
          <p class="mb-0">
            Please complete this form with any questions <br />
            or messages you would like to send. Submissions are stored in the library database.
          </p>
        </div>
        <div class="card col-lg feature-card contact align-items-center justify-content-center pt-4" id="contact-form">
          <h1 class="h2">Contact Us</h1>
          <hr />
          <form class="container needs-validation p-4 pt-0" action="contact_submit.php" method="post" novalidate>
            <div class="mb-3 col-lg">
              <label for="first-name" class="form-label">First Name</label>
              <input type="text" class="form-control" id="first-name" name="first_name" required autocomplete="given-name" />
              <div class="valid-feedback">Looks good!</div>
            </div>
            <div class="mb-3 col-lg">
              <label for="last-name" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="last-name" name="last_name" required autocomplete="family-name" />
              <div class="valid-feedback">Looks good!</div>
            </div>
            <div class="mb-3">
              <label for="contact-email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="contact-email" name="email" aria-describedby="emailHelp" required autocomplete="email" />
              <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
              <label for="validationTextarea" class="form-label">Message</label>
              <textarea class="form-control" id="validationTextarea" name="message" rows="4" aria-label="Message" placeholder="Your message..." required></textarea>
              <div class="invalid-feedback">Please enter a message in the textarea.</div>
            </div>
            <button type="submit" class="btn text-light" style="background-color: rgb(0, 54, 82)">Submit</button>
          </form>
        </div>
      </div>
    </div>

    <footer class="bg-primary text-white py-4 mt-auto">
      <div class="container text-center">
        <p class="small mb-0">
          <a href="about.html" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">About</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          <a href="contact.php" class="link-light link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Contact</a>
          <span class="text-white text-opacity-50 mx-2 user-select-none" aria-hidden="true">·</span>
          © 2026 SmartLib Management. All rights reserved.
        </p>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./script.js"></script>
  </body>
</html>
