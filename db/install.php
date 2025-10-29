<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Install hook for mod_assessment
 */
function xmldb_assessment_install() {
    global $CFG;

    $plugindir = $CFG->dirroot . '/mod/assessment';

    // Composer install sets up the Symfony vendor directory according to composer.lock
    $composer = escapeshellcmd("composer install -d $plugindir");
    exec($composer, $output, $returnCode);
    if ($returnCode !== 0) {
        debugging("Composer install failed: " . implode("\n", $output), DEBUG_DEVELOPER);
    }

    // Path to PHP
    $php = escapeshellcmd($CFG->pathtophp ?? 'php');

    // Path to Doctrine console
    $console = "$plugindir/bin/console";

    // Make sure that Doctrine console is executable
    if (!chmod($console, 0777)) {
        debugging('Changing permissions on bin/console failed', DEBUG_DEVELOPER);
    }

    // Run in Doctrine migrations
    exec("$php $console migrations:migrate --no-interaction", $output, $returnCode);
    if ($returnCode !== 0) {
        debugging("Doctrine migrations failed: " . implode("\n", $output), DEBUG_DEVELOPER);
    }

    return true;
}
