<?php
/**
 * Question behaviour: stackmathgame
 *
 * A thin wrapper around adaptivemultipart that suppresses native UI elements
 * (Check button, feedback containers, validation hints) so the stackmathgame
 * JS layer can own the full interaction surface.
 *
 * Hard dependency for local_stackmathgame — not optional.
 *
 * @package    qbehaviour_stackmathgame
 * @copyright  2025 Your Institution
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$plugin->component    = 'qbehaviour_stackmathgame';
$plugin->version      = 2025010100;
$plugin->requires     = 2024100700; // Moodle 4.5
$plugin->maturity     = MATURITY_ALPHA;
$plugin->release      = '0.1.0';
$plugin->dependencies = [
    'qtype_stack'               => ANY_VERSION,
    'qbehaviour_adaptivemultipart' => ANY_VERSION,
];
