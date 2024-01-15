<?php

/** This file loads all files of specific folder. */
function auto_load_files(STRING $files)
{
	/** Getting the file's path. */
	$file = __DIR__ . '\\' . $files . '.php';

	/** Checking the files exists or not. */
	if (file_exists($file)) {
		/** Including the files. */
		require_once $file;
	}
}

/** Registry for autoload file for `auto_load_files` */
spl_autoload_register('auto_load_files');