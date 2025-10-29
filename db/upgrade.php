<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_assessment_upgrade($oldversion) {
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

    // Run in new Doctrine migrations
    exec("$php $console migrations:migrate --no-interaction", $output, $returnCode);
    if ($returnCode !== 0) {
        debugging("Doctrine migrations failed: " . implode("\n", $output), DEBUG_DEVELOPER);
    }

    return true;
}
