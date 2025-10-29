<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Uninstall hook for mod_assessment
 */
function xmldb_assessment_uninstall() {
    global $CFG;

    $plugindir = $CFG->dirroot . '/mod/assessment';

    // Path to PHP
    $php = escapeshellcmd($CFG->pathtophp ?? 'php');

    // Path to Doctrine console
    $console = "$plugindir/bin/console";

    // Make sure that Doctrine console is executable
    if (!chmod($console, 0700)) {
        debugging('Changing permissions on bin/console failed', DEBUG_DEVELOPER);
    }

    // Roll back all Doctrine migrations
    exec("$php $console migrations:migrate first --no-interaction", $output, $returnCode);
    if ($returnCode !== 0) {
        debugging("Doctrine migration reset failed: " . implode("\n", $output), DEBUG_DEVELOPER);
    }

    // Remove the vendor directory
    $vendorDir = "$plugindir/vendor";
    if (is_dir($vendorDir)) {
        exec("rm -rf " . escapeshellarg($vendorDir));
    }

    return true;
}
