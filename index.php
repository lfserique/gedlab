<?php
require_once 'functions.php';

if (current_user()) {
    redirect('dashboard.php');
}
redirect('login.php');