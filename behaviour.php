<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/behaviour/adaptivemultipart/behaviour.php');
require_once($CFG->libdir . '/outputcomponents.php');

/**
 * STACK Math Game question behaviour.
 *
 * Extends adaptivemultipart but changes the rendering contract so that the
 * local plugin can control the attempt UI.
 */
class qbehaviour_stackmathgame extends qbehaviour_adaptivemultipart {
    public function get_name(): string {
        return 'stackmathgame';
    }

    public function controls(question_attempt $qa, question_display_options $options): string {
        return '';
    }

    public function feedback(question_attempt $qa, question_display_options $options): string {
        $feedback = parent::feedback($qa, $options);
        if ($feedback === '') {
            return '';
        }

        return html_writer::div(
            $feedback,
            'smg-native-feedback visually-hidden',
            ['aria-hidden' => 'true']
        );
    }

    public function question_outer_attributes(
        question_attempt $qa,
        question_display_options $options,
        int $number
    ): array {
        $attrs = parent::question_outer_attributes($qa, $options, $number);
        $attrs['data-smg-controlled'] = 'true';
        $attrs['data-smg-behaviour'] = 'stackmathgame';
        $attrs['data-smg-slot'] = (string)$qa->get_slot();
        $attrs['data-smg-questionid'] = (string)$qa->get_question_id();

        $question = $qa->get_question();
        if (method_exists($question, 'get_expected_data')) {
            $keys = array_keys($question->get_expected_data());
            $keys = array_filter($keys, static function(string $key): bool {
                return !str_ends_with($key, '_val') && !str_ends_with($key, '_type');
            });
            $attrs['data-smg-inputcount'] = (string)count($keys);
        }

        return $attrs;
    }
}
