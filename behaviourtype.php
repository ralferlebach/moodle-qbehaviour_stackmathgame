<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

/**
 * Behaviour type information for stackmathgame.
 */
class qbehaviour_stackmathgame_type extends question_behaviour_type {
    /**
     * Questions can finish during the attempt, matching adaptive STACK flows.
     *
     * @return bool
     */
    public function can_questions_finish_during_the_attempt(): bool {
        return true;
    }
}
