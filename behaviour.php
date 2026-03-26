<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/behaviour/adaptivemultipart/behaviour.php');
require_once($CFG->libdir . '/outputcomponents.php');

/**
 * STACK Math Game question behaviour.
 *
 * First runnable adapter version:
 * - keeps the native adaptive controls available so quiz attempts remain usable
 * - adds stable data attributes for the stackmathgame frontend
 * - wraps native feedback and controls in predictable containers
 */
class qbehaviour_stackmathgame extends qbehaviour_adaptivemultipart {
    /**
     * Name of this behaviour.
     *
     * @return string
     */
    public function get_name(): string {
        return 'stackmathgame';
    }

    /**
     * Keep native controls for now so the behaviour is runnable without custom JS.
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    public function controls(question_attempt $qa, question_display_options $options): string {
        $controls = parent::controls($qa, $options);
        if ($controls === '') {
            return '';
        }

        return html_writer::div($controls, 'smg-native-controls', [
            'data-smg-controls' => 'native',
            'data-smg-slot' => (string)$qa->get_slot(),
        ]);
    }

    /**
     * Wrap the native feedback in a stable container.
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    public function feedback(question_attempt $qa, question_display_options $options): string {
        $feedback = parent::feedback($qa, $options);
        if ($feedback === '') {
            return '';
        }

        return html_writer::div($feedback, 'smg-native-feedback', [
            'data-smg-feedback' => 'native',
            'data-smg-slot' => (string)$qa->get_slot(),
        ]);
    }

    /**
     * Add a stable DOM contract for the stackmathgame runtime.
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @param int $number
     * @return array
     */
    public function question_outer_attributes(
        question_attempt $qa,
        question_display_options $options,
        int $number
    ): array {
        $attrs = parent::question_outer_attributes($qa, $options, $number);
        $attrs['data-smg-controlled'] = '1';
        $attrs['data-smg-behaviour'] = 'stackmathgame';
        $attrs['data-smg-slot'] = (string)$qa->get_slot();
        $attrs['data-smg-questionid'] = (string)$qa->get_question_id();
        $attrs['data-smg-state'] = $qa->get_state()->to_string();
        $attrs['data-smg-sequencecheck'] = (string)$qa->get_sequence_check_count();
        $attrs['data-smg-hasresponse'] = $qa->has_response_to_submit() ? '1' : '0';

        $question = $qa->get_question();
        if (method_exists($question, 'get_expected_data')) {
            $keys = array_keys($question->get_expected_data());
            $keys = array_values(array_filter($keys, static function(string $key): bool {
                return !str_ends_with($key, '_val') && !str_ends_with($key, '_type');
            }));
            $attrs['data-smg-inputcount'] = (string)count($keys);
            if (!empty($keys)) {
                $attrs['data-smg-inputnames'] = implode(',', $keys);
            }
        }

        return $attrs;
    }
}
