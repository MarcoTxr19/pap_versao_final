<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta lang="en">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>G&T Talk</title>
  <link rel="stylesheet" href="<?=base_url('assets/css/boots/bootstrap.min.css')?>" >
  <script src="<?=base_url('assets/js/boots/bootstrap.js')?>" ></script>
  <script src="<?=base_url('assets/js/boots/popper.min.js')?>" ></script>

  <meta name="author" content="Marco Teixeira">

</head>

<body>

  <header id="site-header" style="margin-bottom: 30px">

    <nav class="navbar navbar-expand-lg navbar-light bg-light" style="position: top-sticky;">

      <img src="<?= base_url('uploads/icon.png') ?>" width="50" height="50" class="d-inline-block align-top" alt="">
      
    

        <ul class="navbar-nav">
          <li class="nav-link"><a class="nav-link" href="<?= base_url('') ?>">Home</a></li>
          <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true) : ?>
            <?php if ($_SESSION['is_admin'] == true) : ?>
              <li class="nav-link"><a class="nav-link" href="<?= base_url('admin') ?>">Admin</a></li>
            <?php endif; ?>


            <li class="nav-link"><a class="nav-link" href="<?= base_url('info') ?>">Help</a></li>
            <li class="nav-link"><a class="nav-link" href="<?= base_url('feed') ?>">Feed</a></li>
            <li class="nav-link"><a class="nav-link" href="<?= base_url('user/' . $_SESSION['username']) ?>">Profile</a></li>
            <li class="nav-link"><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
          <?php else : ?>
            <li class="nav-link"><a class="nav-link" href="<?= base_url('register') ?>">Register</a></li>
            <li class="nav-link"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
          <?php endif; ?>
        </ul>

    </nav>



  </header>
  <main id="site-content" role="main">