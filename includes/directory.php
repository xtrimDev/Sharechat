<?php

/** This is the directory and files configuration file. */
class dir
{
    public function current_file()
    {
        return str_replace('\\', '/', __FILE__);
    }

    public function home()
    {
        return str_replace('\\', '/', strstr($this->current_file(), 'admin', true));
    }
}