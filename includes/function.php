<?php

/**
 * This file used to make functions.
 */

function remove_dash(STRING $string): string
{
    return ucwords(str_replace('-', ' ', strtolower($string)));
}

function goto_login()
{
    /** Go to the login page. */
    header('location: auth.php');
}

function goto_home()
{
    /** Go to the home page. */
    header('location: index.php');
}

function rand_str(INT $length, BOOL $specialChar = true): string
{
    $characters = "01234567890abcdefghijklmnopqrstuvwxyz";
    if ($specialChar) {
        $characters .= "`'~!@#$%^&*()_+{}|:<>?,./;[]\-=";
    }

    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $string .= $characters[$index];
    }

    return $string;
}

function generateFileName($originalName): string
{
    $timestamp = date("YmdHis");
    $randomString = rand_str(6, false);
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

    return "{$timestamp}_{$randomString}.{$fileExtension}";
}

function partiallyhideEmailAddress($email)
{
    // use FILTER_VALIDATE_EMAIL filter to validate an email address
    if(filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        // split an email by "@"
        list($first, $last) = explode("@", $email);

        // get half the length of the first part
        $len = floor(strlen($first)/2);

        // partially hide a string by "*" and return full string
        return substr($first, 0, $len) . str_repeat('*', $len) . "@" . $last;
    } else {
        return false;
    }
}