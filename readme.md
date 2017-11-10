CAMRA Auth Wordpress Plugin
===========================

## Introduction

A Wordpress plugin for authenticating CAMRA members.

## Installation

[Download Wordpress](https://wordpress.org/download/) and clone this repository 
in the `wp-content/plugins` directory.

    git submodule add git@github.com:tomblakemore/camra-auth-wp-plugin wp-content/plugins/camra-auth

## Setup

Open the Wordpress admin, navigation to the `Plugins` tab and activate the 
`CAMRA Auth` plugin. Once active go to the `Settings > CAMRA Auth` menu and 
fill in your branch code and API key.

Create and publish two new pages - `/login` and `/members`. Edit the login page 
and add the form tag to the body and save.

    [camra_auth_login_form]

If you would like to place a link/button somewhere which says login when there
is no logged in user, or logout when there is then use a code snippet like the 
below:

    <?php
    if (!is_camra_auth_member_logged_in()) {
        ?><a href="<?php echo home_url( '/members/' ); ?>">Login to Members' Area</a><?php
    } else {
        ?><a href="<?php echo home_url( '/members/' ); ?>">Members' Area</a> | <a href="<?php echo home_url( '/logout/' ); ?>">Logout</a><?php
    }
    ??>
