<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'qbehaviour_stackmathgame';
$plugin->version   = 2026032500;
$plugin->requires  = 2024100700; // Moodle 4.5.
$plugin->maturity  = MATURITY_ALPHA;
$plugin->release   = '0.2.0';
$plugin->dependencies = [
    'qtype_stack' => ANY_VERSION,
];
