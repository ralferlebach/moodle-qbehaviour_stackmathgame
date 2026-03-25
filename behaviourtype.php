<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

/**
 * Behaviour type information for stackmathgame.
 */
class qbehaviour_stackmathgame_type extends question_behaviour_type {
    public function can_questions_finish_during_the_attempt(): bool {
        return true;
    }
}
