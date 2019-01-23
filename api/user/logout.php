<?php
  session_start();
  session_unset();
  session_destroy();
  unset($_COOKIE['user_id']);
  header('location:/login');
